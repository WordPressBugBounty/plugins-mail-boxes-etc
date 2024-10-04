<?php

class Mbe_Shipping_Helper_Logger
{
	public static $log = true;

	private $helper;
	private $pluginVersionMessage;

	public function __construct()
	{
		$this->helper = new Mbe_Shipping_Helper_Data();
		$this->pluginVersionMessage = MBE_ESHIP_PLUGIN_NAME.' version ' . MBE_ESHIP_PLUGIN_VERSION . ' :';
	}

	public function log($message, $force = false)
	{
		if ($this->helper->debug() || $force) {


			$logDirPath = $this->helper->getMbeLogDir(); // returns the path checking if it exists and if it's missing it creates it

			// if the directory already exists but the permissions are wrongs, changes it
			if(file_exists($logDirPath) && substr(sprintf('%o', fileperms($logDirPath)), -4)<>0755 ) {
				chmod( $logDirPath, 0755 );
			}

//			if(substr(sprintf('%o', fileperms(MBE_ESHIP_PLUGIN_LOG_DIR)), -4)<>0755 ) {
//				chmod( MBE_ESHIP_PLUGIN_LOG_DIR, 0755 );
//			}
//
//			if (!file_exists(MBE_ESHIP_PLUGIN_LOG_DIR ) ) {
//				mkdir(MBE_ESHIP_PLUGIN_LOG_DIR , 0755, true);
//			}

			if(!file_exists(trailingslashit($logDirPath) . '.htaccess')) {
				$this->helper->createHtaccessDenyAll($logDirPath);
			}

			$row = date_format(new DateTime(), 'Y-m-d\TH:i:s\Z');
			$row .= " - ";
			$row .= $this->pluginVersionMessage . $message . "\n\r";
			file_put_contents($this->helper->getLogPluginPath(), $row, FILE_APPEND);
		}
	}


	public function logVar($var, $message = null, $force = false)
	{
		if ($message) {
			$this->log($message, $force);
		}
		$this->log(print_r($var, true),$force);
	}
}