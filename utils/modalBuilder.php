<?php
    class ModalBuilder {
        private $tableName;
        private $columns = [];
        private $dropdownColumns = [];
        private $requiredColumns = [];
        private $hiddenColumns = [];
        private $modalId;
        private $insertHandler;
            
        public function setTableName($tableName) {
            $this->tableName = $tableName;
            return $this;
        }
    
        public function setModalId($modalId = "modal") {
            $this->modalId = $modalId;
            return $this;
        }

        public function setInsertHandler(InsertHandler $handler) {
            $this->insertHandler = $handler;
            return $this;
        }
    
        public function addColumn($column, $optional = false) {
            $this->columns[] = $column;
            
            if (!$optional) {
                $this->requiredColumns[] = $column;
            }

            return $this;
        }

        public function addHiddenColumn($column, $value) {
            $this->hiddenColumns[$column] = $value;
            return $this;
        }

        public function addDropdownColumn($column, $values) {
            $this->dropdownColumns[$column] = $values;
            return $this;
        }
    
        public function generateOpenButton($label = "Open Modal") {
            return "<button type='button' class='btn btn-primary' data-toggle='modal' data-target='#{$this->modalId}'>{$label}</button>";
        }

        public function handleData() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tableName']) && $_POST['tableName'] === $this->tableName) {
                $this->insertHandler->handleInsert($_POST);
            }
        }

        public function build() {
            $modalStart = "<form action='' method='POST'>
                            <div class='modal fade' id='{$this->modalId}' tabindex='-1' role='dialog' aria-labelledby='insertModalLabel' aria-hidden='true'>
                                <div class='modal-dialog' role='document'>
                                    <div class='modal-content rounded'>
                                        <div class='modal-header'>
                                            <button type='button' class='close ml-0' data-dismiss='modal' aria-label='Close'>
                                                <span aria-hidden='true'>&times;</span>
                                            </button>
                                            <h5 class='modal-title'>Insert into: {$this->tableName}</h5>
                                        </div>
                                        <div class='modal-body'>";

            $modalBody = '';
            foreach ($this->columns as $column) {
                $required = in_array($column, $this->requiredColumns) ? 'required' : '';
                $modalBody .= "<div class='form-group'>
                                    <label for='$column'>$column</label>
                                    <input type='text' class='form-control' id='$column' name='$column' placeholder='$column' $required>
                                </div>";
            }

            foreach ($this->dropdownColumns as $column => $values) {
                $options = '';
                foreach ($values as $value) {
                    $options .= "<option value='{$value}'>{$value}</option>";
                }
                $modalBody .= "<div class='form-group'>
                                    <label for='$column'>$column</label>
                                    <select class='form-control' id='$column' name='$column' required>
                                        $options
                                    </select>
                                </div>";
            }

            foreach ($this->hiddenColumns as $column => $value) {
                $modalBody .= "<input type='hidden' name='$column' value='$value'>";
            }

            $modalEnd = "</div>
                            <div class='modal-footer'>
                                <input type='hidden' name='tableName' value='{$this->tableName}'>
                                <button type='submit' class='btn btn-primary'>Save</button>
                            </div>
                        </div>
                    </div>
                    </div>
                </form>";

            return $modalStart . $modalBody . $modalEnd;
        }
    }
?>