<?php
    interface PostHandler {
        public function handlePostData($data);
        public function getOperationType();
    }
?>