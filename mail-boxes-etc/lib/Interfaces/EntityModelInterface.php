<?php

interface Mbe_Shipping_Entity_Model_Interface
{
	public function getTableName();
	public function getSqlCreate();
	public function truncate();
	public function insertRow($row);
	public function tableExists();
}