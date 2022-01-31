<?php namespace Organizer;

class Exception extends \RuntimeException {
    const INVALID_NAME = 0x01;
    const NO_SUCH_FILE = 0x02;
    const NOT_READABLE = 0x03;
    const UNKNOWN_PATH = 0x04;
}