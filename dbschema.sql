DROP DATABASE IF EXISTS a22willi;
CREATE DATABASE a22willi;
USE a22willi;

/* --- TABLE DEFINITIONS --- */

CREATE TABLE Agent (
	CodeName		CHAR(3) UNIQUE NOT NULL,
    FirstName		VARCHAR(32) NOT NULL,
    LastName		VARCHAR(32) NOT NULL,
    Salary			DECIMAL,
    IsGroupLeader	BOOL DEFAULT 0,
    IsManager		BOOL DEFAULT 0,
    IsFieldAgent	BOOL DEFAULT 1,
    CHECK 			(CodeName RLIKE '^[a-zA-Z][0-9]{1,2}$'), -- Only accept ex: 'B23' or 'x5'.
    CHECK 			(LENGTH(FirstName) > 0 AND LENGTH(LastName) > 0),
    CHECK 			(Salary >= 0),
    PRIMARY KEY 	(CodeName)
) ENGINE = INNODB;

CREATE INDEX Name ON Agent(FirstName, LastName) USING BTREE;

-- Denormalization (Horizontal split)
CREATE TABLE FieldAgentAttributes (
	AgentCodeName	CHAR(3) UNIQUE NOT NULL, -- Prevent multiple FieldAgentAttributes per Agent using unique.
	Specialty		VARCHAR(256),
    Competence		VARCHAR(256),
    CHECK 			(Specialty IS NULL OR LENGTH(Specialty) > 0),
    CHECK 			(Competence IS NULL OR LENGTH(Competence) > 0),
    PRIMARY KEY 	(AgentCodeName),
    FOREIGN KEY 	(AgentCodeName) REFERENCES Agent(CodeName)
) ENGINE = INNODB;

-- Denormalization (Codes)
CREATE TABLE Terrain (
	TerrainCode		TINYINT UNSIGNED UNIQUE NOT NULL,
    TerrainName		VARCHAR(64),
    PRIMARY KEY 	(TerrainCode)
) ENGINE = INNODB;

CREATE INDEX TerrainTypes ON Terrain(TerrainName);

-- Denormalization (Merge)
CREATE TABLE Incident (
	RegionName		VARCHAR(32) NOT NULL,
    Terrain			TINYINT UNSIGNED NOT NULL,
	IncidentName	VARCHAR(64) NOT NULL,
    IncidentNumber	INT UNIQUE NOT NULL,
    Location		VARCHAR(128),
    PRIMARY KEY 	(IncidentName, IncidentNumber),
    FOREIGN KEY 	(Terrain) REFERENCES Terrain(TerrainCode)
) ENGINE = INNODB;

-- Denormalization (Vertical split)
CREATE TABLE Report (
	DateCreated 	DATETIME NOT NULL,
    Author			CHAR(3) NOT NULL,
    Title			VARCHAR(64) NOT NULL,
    Content			VARCHAR(1024),
    IncidentName	VARCHAR(64),
    IncidentNumber	INT,
    PRIMARY KEY 	(DateCreated, Title),
    FOREIGN KEY		(Author) REFERENCES Agent(CodeName),
	FOREIGN KEY 	(IncidentName, IncidentNumber) REFERENCES Incident(IncidentName, IncidentNumber)
) ENGINE = INNODB;

-- Denormalization (Vertical split)
CREATE TABLE ArchivedReport (
	DateCreated 	DATETIME NOT NULL,
    Author			CHAR(3) NOT NULL,
    Title			VARCHAR(64) NOT NULL DEFAULT 'nameless_report',
    Content			VARCHAR(1024),
    IncidentName	VARCHAR(64),
    IncidentNumber	INT,
    PRIMARY KEY 	(DateCreated, Title),
	FOREIGN KEY		(Author) REFERENCES Agent(CodeName),
	FOREIGN KEY 	(IncidentName, IncidentNumber) REFERENCES Incident(IncidentName, IncidentNumber)
) ENGINE = INNODB;

CREATE TABLE Operation (
	OperationName	VARCHAR(128) NOT NULL,
    StartDate		DATE NOT NULL,
    EndDate			DATE,
    SuccessRate		BIT,
    GroupLeader		CHAR(3),
    IncidentName	VARCHAR(64) NOT NULL,
    IncidentNumber	INT NOT NULL,
    CHECK 			(EndDate IS NULL OR EndDate >= StartDate),
	PRIMARY KEY 	(OperationName, StartDate, IncidentName, IncidentNumber),
    FOREIGN KEY 	(IncidentName, IncidentNumber) REFERENCES Incident(IncidentName, IncidentNumber),
    FOREIGN KEY 	(GroupLeader) REFERENCES Agent(CodeName)
) ENGINE = INNODB;

-- N - M relation (Agent, Operation)
CREATE TABLE OperatesIn (
	IncidentName	VARCHAR(64) NOT NULL,
    IncidentNumber	INT NOT NULL,
    OperationName	VARCHAR(128) NOT NULL,
    StartDate		DATE NOT NULL,
    CodeName		CHAR(3) NOT NULL,
    PRIMARY KEY 	(IncidentName, IncidentNumber, OperationName, StartDate, CodeName),
    FOREIGN KEY 	(IncidentName, IncidentNumber) REFERENCES Incident(IncidentName, IncidentNumber),
    FOREIGN KEY 	(OperationName, StartDate) REFERENCES Operation(OperationName, StartDate),
    FOREIGN KEY 	(CodeName) REFERENCES Agent(CodeName)
) ENGINE = INNODB;

/* --- LOG TABLES --- */

CREATE TABLE AgentLog (
	Id 				BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    Operation		VARCHAR(10),
    UserName		VARCHAR(32),
    CodeName		CHAR(3),
    OperationTime	DATETIME,
    PRIMARY KEY 	(Id)
) ENGINE = INNODB;

/* --- STORED PROCEDURES --- */

DELIMITER //

-- Get the number of active operations an agent has. 
-- Conditional procedure with error handling
CREATE PROCEDURE GetNumberOfActiveOperations(IN _Agent CHAR(3), OUT _NumOfActiveOperations SMALLINT UNSIGNED)
BEGIN
	IF (SELECT COUNT(*) FROM Agent WHERE CodeName = _Agent) = 0
		THEN SET @message = CONCAT('Agent: ', _Agent, ' does not exist.');
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = @message;
	END IF;
    
	SELECT COUNT(OpIn.OperationName) AS SumOfOperations INTO _NumOfActiveOperations 
	FROM OperatesIn OpIn
    JOIN Operation Op ON OpIn.OperationName = Op.OperationName AND OpIn.StartDate = Op.StartDate
    WHERE OpIn.CodeName = _Agent AND (Op.EndDate IS NULL OR Op.EndDate > CURDATE());
END//

-- Simple procedure
CREATE PROCEDURE GetAgentNamesAlphabetically()
BEGIN
	SELECT FirstName, LastName, CodeName
    FROM Agent
    ORDER BY LastName, FirstName;
END//

CREATE PROCEDURE GetOperationRegion(IN _OpName VARCHAR(128), OUT _Region VARCHAR(32))
BEGIN
	SELECT Inc.RegionName INTO _Region
    FROM Incident Inc
    JOIN Operation Op ON Op.IncidentName = Inc.IncidentName AND Op.IncidentNumber = Inc.IncidentNumber
    WHERE Op.OperationName = _OpName;
END//

-- Move report from Report to ArchivedReport
-- Procedure that moves tuple in vertical split
CREATE PROCEDURE ArchiveReport(IN _DateCreated DATETIME, IN _Title VARCHAR(64))
BEGIN
    INSERT INTO ArchivedReport (DateCreated, Author, Title, Content, IncidentName, IncidentNumber) 
    SELECT R.DateCreated, R.Author, R.Title, R.Content, R.IncidentName, R.IncidentNumber
    FROM Report R 
    WHERE R.DateCreated = _DateCreated AND R.Title = _Title;
    
    DELETE FROM Report
    WHERE Report.DateCreated = _DateCreated AND Report.Title = _Title;
END //

-- get Operations between two dates
-- procedure that selects data
CREATE PROCEDURE GetOperationsInRange(IN _StartDate DATE, IN _EndDate DATE)
BEGIN 
	SELECT * 
    FROM Operation Op
    WHERE Op.StartDate > _StartDate 
    AND Op.EndDate IS NOT NULL AND Op.EndDate < _EndDate;
END//

/* --- TRIGGERS --- */

-- Log Agent operations
CREATE TRIGGER AgentLogInsert AFTER INSERT ON Agent
FOR EACH ROW BEGIN
	INSERT INTO AgentLog (Operation, UserName, CodeName, OperationTime) VALUES
    ('INSERT', USER(), NEW.CodeName, NOW());
END//

CREATE TRIGGER AgentLogUpdate AFTER UPDATE ON Agent
FOR EACH ROW BEGIN
	INSERT INTO AgentLog (Operation, UserName, CodeName, OperationTime) VALUES
    ('UPDATE', USER(), NEW.CodeName, NOW());
END//

CREATE TRIGGER AgentLogDelete AFTER DELETE ON Agent
FOR EACH ROW BEGIN
	INSERT INTO AgentLog (Operation, UserName, CodeName, OperationTime) VALUES
    ('DELETE', USER(), OLD.CodeName, NOW());
END//

-- Validate that an agent is part of at least one group, fieldagents, managers or groupleaders.
CREATE TRIGGER ValidateAgent BEFORE INSERT ON Agent
FOR EACH ROW BEGIN
	IF NEW.IsGroupLeader = 0 AND NEW.IsManager = 0 AND NEW.IsFieldAgent = 0 
		THEN 
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Agent missing type (eg: FieldAgent).';
    END IF;
END//

-- Before deleting an incident, also remove all related operations and reports. 
CREATE TRIGGER IncidentCleanup BEFORE DELETE ON Incident
FOR EACH ROW BEGIN
    DELETE FROM Report
    WHERE IncidentName = OLD.IncidentName AND IncidentNumber = OLD.IncidentNumber;
    
    DELETE FROM ArchivedReport
    WHERE IncidentName = OLD.IncidentName AND IncidentNumber = OLD.IncidentNumber;
    
    DELETE FROM OperatesIn
    WHERE IncidentName = OLD.IncidentName AND IncidentNumber = OLD.IncidentNumber;
    
    DELETE FROM Operation
    WHERE IncidentName = OLD.IncidentName AND IncidentNumber = OLD.IncidentNumber;
END//

-- Validate that an agent can only be part of Operations in the same region, 
-- And at most 3 operations in the same region. 
CREATE TRIGGER ValidateOperatesIn BEFORE INSERT ON OperatesIn
FOR EACH ROW BEGIN
	DECLARE MaxAllowedOperations INT DEFAULT 3;
	DECLARE ExistingRegion VARCHAR(32) DEFAULT NULL;
    DECLARE NewRegion VARCHAR(32) DEFAULT NULL;
    DECLARE NumOfOperations SMALLINT UNSIGNED DEFAULT NULL;

    -- Get the region of the new operation the agent is being added to
    CALL GetOperationRegion(NEW.OperationName, NewRegion);
    
    -- Get the current number of active operations an agent has
    CALL GetNumberOfActiveOperations(NEW.CodeName, NumOfOperations);

    -- Get the region of an existing operation the agent is part of
    SELECT Inc.RegionName INTO ExistingRegion
    FROM Incident Inc, Operation Op, OperatesIn OpIn
    WHERE Op.OperationName = OpIn.OperationName AND Op.StartDate = OpIn.StartDate AND Op.IncidentName = Inc.IncidentName AND Op.IncidentNumber = Inc.IncidentNumber
    AND OpIn.CodeName = NEW.CodeName AND (Op.EndDate IS NULL OR Op.EndDate > CURDATE())
    LIMIT 1;

    -- If the agent is part of an operation in a different region, raise an error
    IF ExistingRegion IS NOT NULL AND ExistingRegion != NewRegion THEN
		SET @message = CONCAT('Agent already has ongoing operation in region:', ExistingRegion);
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = @message;
	END IF;
    
    -- If the agent is part already part of 3 operations, raise an error
	IF NumOfOperations >= MaxAllowedOperations THEN 
		SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Agent can be part of no more than 3 operations in the same region.';
    END IF;
END//

CREATE TRIGGER ValidateOperation BEFORE INSERT ON Operation
FOR EACH ROW
BEGIN
    IF NEW.EndDate > DATE_ADD(NEW.StartDate, INTERVAL 5 WEEK) THEN
        SET NEW.EndDate = DATE_ADD(NEW.StartDate, INTERVAL 5 WEEK);
    END IF;
END//


DELIMITER ;

/* --- VIEWS --- */

-- Specialization
-- Get the average successrate for all agents
CREATE VIEW AgentRates AS
SELECT 
    OpIn.CodeName,
    AVG(Op.SuccessRate) AS AvgSuccessRate
FROM OperatesIn OpIn, Operation Op
WHERE 
    OpIn.OperationName = Op.OperationName
    AND OpIn.StartDate = Op.StartDate
GROUP BY OpIn.CodeName;

-- Simplification (and specialization)
-- Get all field agents, including their competence, specialization and average successrate.
CREATE VIEW FieldAgents AS
SELECT Agent.CodeName, Firstname, LastName, Salary, Specialty, Competence, AvgSuccessRate
FROM Agent, FieldAgentAttributes, AgentRates
WHERE Agent.IsFieldAgent
AND Agent.CodeName = FieldAgentAttributes.AgentCodeName
AND Agent.CodeName = AgentRates.CodeName;

-- Simplification
-- Get all GroupLeaders
CREATE VIEW GroupLeaders AS
SELECT Agent.CodeName, FirstName, LastName, Salary, AvgSuccessRate
FROM Agent, AgentRates
WHERE IsGroupLeader
AND Agent.CodeName = AgentRates.CodeName;

-- Simplification
-- Get all managers
CREATE VIEW Managers AS
SELECT Agent.CodeName, FirstName, LastName, Salary, AvgSuccessRate
FROM Agent, AgentRates
WHERE IsManager
AND Agent.CodeName = AgentRates.CodeName;

-- Simplification
CREATE VIEW AllReports AS
SELECT * 
FROM Report
UNION ALL
SELECT *
FROM ArchivedReport;

CREATE VIEW AgentOperations AS
SELECT A.CodeName, OpIn.OperationName
FROM Agent A, OperatesIn OpIn
WHERE A.CodeName = OpIn.CodeName;

/* --- GENERATE MOCK DATA --- */

INSERT INTO Terrain (TerrainName, TerrainCode) VALUES 
	('Mountainous', 1),
	('Plains', 2),
	('Hills', 3),
	('Desert', 4),
	('Forest', 5);

INSERT INTO Agent (CodeName, FirstName, LastName, Salary, IsGroupLeader, IsManager, IsFieldAgent) VALUES
	('A1', 'John', 'Doe', 50000, TRUE, FALSE, TRUE),
	('B23', 'Jane', 'Smith', 52000, FALSE, TRUE, FALSE),
	('C45', 'Alex', 'Johnson', 51000, FALSE, FALSE, TRUE),
	('D56', 'Emily', 'Brown', 50500, TRUE, FALSE, FALSE),
	('E78', 'Michael', 'Davis', 53000, FALSE, TRUE, TRUE);

INSERT INTO Incident (RegionName, Terrain, IncidentName, IncidentNumber, Location) VALUES
	('North', 1, 'Heist', 101, 'North Town'),
	('West', 2, 'Kidnapping', 102, 'South City'),
	('North', 3, 'Sabotage', 103, 'East Village'),
	('South', 4, 'Espionage', 104, 'West Point'),
	('NorthEast', 3, 'Breach', 105, 'Central Hub'),
    ('SouthWest', 2, 'Ambush', 106, 'Mountain Pass'),
	('MidWest', 4, 'Hijacking', 107, 'River Crossing'),
	('Central', 1, 'Arson', 108, 'Forest Edge'),
	('OuterWest', 3, 'Assault', 109, 'Desert Base'),
	('DeepSouth', 2, 'Theft', 110, 'Island Shore');

INSERT INTO Operation (OperationName, StartDate, EndDate, SuccessRate, GroupLeader, IncidentName, IncidentNumber) VALUES
	('Operation Thunder', '2023-01-01', '2023-10-18', 1, 'A1', 'Sabotage', 103),
    ('Operation QuickSilver1', '2023-02-05', '2023-02-18', 0, 'D56', 'Heist', 101),
    ('Operation QuickSilver2', '2023-02-05', '2025-10-18', 0, 'D56', 'Heist', 101),
    ('Operation QuickSilver3', '2023-02-05', '2023-11-18', 0, 'D56', 'Heist', 101),
    ('Operation NightFall', '2023-03-10', '2023-03-30', 1, 'A1', 'Sabotage', 103),
    ('Operation DesertStorm', '2023-04-01', NULL, 1, 'E78', 'Espionage', 104),
    ('Operation ForestGuard', '2023-05-01', '2023-05-10', NULL, 'D56', 'Breach', 105);

INSERT INTO OperatesIn (IncidentName, IncidentNumber, OperationName, StartDate, CodeName) VALUES
	('Heist', 101, 'Operation Thunder', '2023-01-01', 'A1'),
	('Heist', 101, 'Operation Thunder', '2023-01-01', 'B23'),
	('Kidnapping', 102, 'Operation QuickSilver1', '2023-02-05', 'B23'),
	('Kidnapping', 102, 'Operation QuickSilver2', '2023-02-05', 'B23'),
	('Kidnapping', 102, 'Operation QuickSilver3', '2023-02-05', 'B23'),
	('Kidnapping', 102, 'Operation QuickSilver1', '2023-02-05', 'C45'),
	('Espionage', 104, 'Operation DesertStorm', '2023-04-01', 'D56'),
	('Breach', 105, 'Operation ForestGuard', '2023-05-01', 'E78');
    
INSERT INTO FieldAgentAttributes (AgentCodeName, Specialty, Competence) VALUES 
    ('A1', 'Explosives Expert', 'Advanced explosives handling and defusal'),
    ('C45', 'Surveillance Specialist', 'Expert in covert surveillance and intelligence gathering'),
    ('E78', 'Combat Specialist', 'Skilled hand-to-hand combat and small arms use');
    
INSERT INTO Report (DateCreated, Author, Title, Content, IncidentName, IncidentNumber) VALUES
    ('2023-04-15', 'A1', 'Update on Heist', 'Latest details on the heist operation. Successful with minor hitches.', 'Heist', 101),
    ('2023-06-10', 'B23', 'Kidnapping in West', 'The kidnapping incident at South City has been contained.', 'Kidnapping', 102),
    ('2023-06-20', 'D56', 'Sabotage Alert', 'Sabotage in East Village has caused significant infrastructure damage.', 'Sabotage', 103),
    ('2023-07-01', 'A1', 'Espionage in South', 'Espionage activities spotted in West Point. Suspects are being tracked.', 'Espionage', 104),
    ('2023-08-15', 'C45','Breach in NorthEast', 'A major breach in Central Hub. Cybersecurity teams are on it.', 'Breach', 105);

INSERT INTO ArchivedReport (DateCreated, Author, Title, Content, IncidentName, IncidentNumber) VALUES
    ('2020-03-05', 'D56', 'Old Heist Report', 'Details on an old heist operation.', 'Heist', 101),
    ('2019-12-10', 'A1', 'Old Kidnapping Details', 'Report on a kidnapping that happened two years back.', 'Kidnapping', 102),
    ('2021-01-20', 'B23', 'Sabotage in East - Historical', 'An old sabotage report detailing the damage.', 'Sabotage', 103),
    ('2021-02-15', 'A1', 'Espionage History', 'Previous espionage activities in West Point.', 'Espionage', 104),
    ('2021-04-05', 'E78', 'Breach Report - Historical', 'A significant breach happened two years back in Central Hub.', 'Breach', 105);