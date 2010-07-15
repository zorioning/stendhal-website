<?php
{
	if(!defined('PHARAUROA_NETWORK_PROTOCOL_VERSION'))
		define('PHARAUROA_NETWORK_PROTOCOL_VERSION', 1);
}

class PharauroaDeserializer {
	// Fields
	private $data;
	private $protocolVersion = PHARAUROA_NETWORK_PROTOCOL_VERSION;

	// Constructors
	public function __construct($data) {
		$this->data = $data;
	}

	// Version
	public function setProtocolVersion($protocolVersion) {
		$this->$protocolVersion = min($protocolVersion, PHARAUROA_NETWORK_PROTOCOL_VERSION);
	}
	public function getProtocolVersion() {
		return $this->protocolVersion;
	}

	// Numeric
	public function readByte() {
		// TODO opposite of: $this->data = $this->data . chr($byte);
		$output = ord($this->data[0]);
		$this->data = substr($this->data, 1);
		return $output; // TODO
	}
	public function readInt() {
		// TODO opposite of: $this->data = $this->data . pack("I", $int);
		$output = unpack("I", $this->data);
		$this->data = substr($this->data, 4);
		return $output[1]; 
	}

	// String
	public function readString() {
		// TODO opposite of: $this->writeInt(strlen($string)); $this->data = $this->data . $string;
		$length = $this->readInt();
		$output = substr($this->data, 0, $length);
		$this->data = substr($this->data, $length);
		return $output;
	}

	public function read255LongString() {
		// TODO opposite of: $this->writeByte(strlen($string)); $this->data = $this->data . $string;
		$length = $this->readByte();
		$output = substr($this->data, 0, $length);
		$this->data = substr($this->data, $length);
		return $output;
	}
}
?>