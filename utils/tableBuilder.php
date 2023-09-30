<?php
    class TableBuilder {

        private $db;
        private $query;
        private $redirectAddress;
        private $queryColumns = [];
        private $actionCallbacks = [];

        public function __construct($query) {
            $this->db = dbconnection::getInstance();
            $this->query = $query;
        }

        public function setRedirect($address, $queryColumns = []) {
            $this->redirectAddress = $address;
            $this->queryColumns = $queryColumns;
            return $this;
        }

        public function addActionColumn($function, $headerTitle = "Action") {
            $this->actionCallbacks[] = ['callback' => $function, 'header' => $headerTitle];
            return $this;
        }

        private function buildRow($row) {
            $rowData = "<tr";

            if ($this->redirectAddress) {
                $queryParams = array_map(function($col) use ($row) {
                    return $col . "=" . urlencode($row[$col]);
                }, $this->queryColumns);
                $redirectUrl = $this->redirectAddress . "?" . implode("&", $queryParams);
                $rowData .= " onclick=\"window.location.href = '" . $redirectUrl . "'\" style='cursor: pointer;'";
            }

            $rowData .= ">";

            foreach ($row as $key => $value) {
                if (!is_numeric($key)) {
                    $rowData .= "<td>" . $value . "</td>";
                }
            }

            foreach ($this->actionCallbacks as $action) {
                $rowData .= "<td style='width: 80px; white-space: nowrap;'>" . call_user_func($action['callback'], $row) . "</td>";
            }
            $rowData .= "</tr>";
            
            return $rowData;
        }

        public function buildTable() {
            $hover = isset($this->redirectAddress) ? "table-hover" : "";
            $output = "<div class='overflow-auto mt-1 mb-1'><table class='table table-striped table-bordered $hover'><thead class='thead-dark'><tr>";

            foreach ($this->db->getPdo()->query($this->query) as $row) {
                foreach ($row as $key => $value) {
                    if (!is_numeric($key)) {
                        $output .= "<th>" . $key . "</th>";
                    }
                }
                foreach ($this->actionCallbacks as $action) {
                    $output .= "<th style='width: 80px; white-space: nowrap;'>" . $action['header'] . "</th>";
                }
                $output .= "</tr></thead><tbody>";
                break; 
            }

            foreach ($this->db->getPdo()->query($this->query) as $row) {
                $output .= $this->buildRow($row);
            }

            $output .= "</tbody></table></div>";
            return $output;
        }
    }
?>