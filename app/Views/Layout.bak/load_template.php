<?php
$this->extend("/Layout/main_template");
$this->section('content');
echo view($view,$data,$options);
$this->endSection()
?>