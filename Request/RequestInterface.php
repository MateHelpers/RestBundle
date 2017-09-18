<?php


namespace Mate\RestBundle\Request;


interface RequestInterface {
	/**
	 * @return array
	 */
	public function rules();
}