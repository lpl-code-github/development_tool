<?php


namespace App\Factory;


class ExceptionFactory
{
    const WrongFormatExceptionCode = 100;
    const MissingInputExceptionCode = 300;
    const NotFoundExceptionCode = 400;
    const DuplicateFoundExceptionCode = 500;
    const NotTokenExceptionCode = 600;
    const ActionNotPerformedExceptionCode = 700;

    static public function WrongFormatException($msg, $code = 400) {
        $ex = (trim($msg) === "") ? new \Exception("WrongFormatException", $code) : new \Exception("WrongFormatException: ".$msg, $code);
        return $ex;
    }

    static public function MissingInputException($msg, $code = 400) {
        $ex = (trim($msg) === "") ? new \Exception("MissingInputException", $code) : new \Exception("MissingInputException: ".$msg, $code);
        return $ex;
    }

    static public function NotFoundException($msg, $code = 404) {
        $ex = (trim($msg) === "") ? new \Exception("NotFoundException", $code) : new \Exception("NotFoundException: ".$msg, $code);
        return $ex;
    }

    static public function DuplicateFoundException($msg, $code = 400) {
        $ex = (trim($msg) === "") ? new \Exception("DuplicateFoundException", $code) : new \Exception("DuplicateFoundException: ".$msg, $code);
        return $ex;
    }

    static public function NotTokenException($msg, $code = 403) {
        return new \Exception($msg, $code);
    }

    static public function NotScopeException($msg, $code = 403) {
        // $ex = (trim($msg) === "") ? new \Exception($code.": NotScopeException", $code) : new \Exception($code.": NotScopeException: ".$msg, $code);
        return new \Exception($msg, $code);
    }

    static public function OAuthNotResponseException($msg, $code = 408) {
        return new \Exception($msg, $code);
    }

    static public function ConflictException($msg, $code = 429) {
        $ex = (trim($msg) === "") ? new \Exception("ConflictException", $code) : new \Exception("ConflictException: ".$msg, $code);
        return $ex;
    }

    static public function ServerLimitExceededException($msg, $code = 413) {
        return new \Exception($msg, $code);
    }

    static public function ActionNotPerformedException($msg, $code = 403) {
        return new \Exception($msg, $code);
    }

    static public function InternalServerException($msg, $code = 500) {
        return new \Exception($msg, $code);
    }

    static public function ArgumentOutOfRangeException($msg, $code = 501) {
        $ex = (trim($msg) === "") ? new \Exception("ArgumentOutOfRangeException", $code) : new \Exception("ArgumentOutOfRangeException: ".$msg, $code);
        return $ex;
    }

}