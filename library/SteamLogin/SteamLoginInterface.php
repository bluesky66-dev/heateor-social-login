<?php
//namespace Ehesp\SteamLogin;
if(!interface_exists('SteamLogin')){
	interface SteamLoginInterface
	{
	    public function url($return);
		public function validate();
	}
}