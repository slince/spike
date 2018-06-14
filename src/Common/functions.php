<?php
namespace Slince\Common;

use Clue\JsonStream\StreamingJsonParser;
use React\Promise;
use React\Stream\ReadableStreamInterface;
use Spike\Common\Exception\RuntimeException;
use Spike\Common\Protocol\HttpHeaderParser;

/**
 * @param ReadableStreamInterface $stream
 * @return Promise\PromiseInterface
 */
function jsonBuffer(ReadableStreamInterface $stream)
{
    // stream already ended => resolve with empty buffer
    if (!$stream->isReadable()) {
        return Promise\resolve('');
    }
    $promise = new Promise\Promise(function ($resolve, $reject) use ($stream, &$bufferer) {
        $streamParser = new StreamingJsonParser();
        $bufferer = function ($data) use ($resolve, $streamParser) {
            $parsed = $streamParser->push($data);
            var_dump('data');
            if ($parsed) {
                $resolve($parsed);
            }
        };
        $stream->on('data', $bufferer);
        $stream->on('error', function ($error) use ($reject) {
            $reject(new RuntimeException('An error occured on the underlying stream while buffering', 0, $error));
        });
        $stream->on('close', function () use ($resolve, $streamParser) {
            $resolve($streamParser->push(''));
        });
    }, function ($_, $reject) {
        $reject(new RuntimeException('Cancelled buffering'));
    });
    return $promise->then(null, function ($error) use (&$buffer, $bufferer, $stream) {
        // promise rejected => clear buffer and buffering
        $buffer = '';
        $stream->removeListener('data', $bufferer);
        throw $error;
    });
}

/**
 * @param ReadableStreamInterface $stream
 * @return Promise\PromiseInterface
 */
function httpHeaderBuffer(ReadableStreamInterface $stream, HttpHeaderParser $parser = null)
{
    // stream already ended => resolve with empty buffer
    if (!$stream->isReadable()) {
        return Promise\resolve('');
    }
    if ($parser === null) {
        $parser = new HttpHeaderParser();
    }
    $promise = new Promise\Promise(function ($resolve, $reject) use ($stream, &$bufferer, $parser) {
        $bufferer = function ($data) use ($resolve, $parser) {
            $parsed = $parser->push($data);
            if ($parsed) {
                $resolve($parsed);
            }
        };
        $stream->on('data', $bufferer);
        $stream->on('error', function ($error) use ($reject) {
            $reject(new RuntimeException('An error occured on the underlying stream while buffering', 0, $error));
        });
        $stream->on('close', function () use ($resolve, $parser) {
            $resolve($parser->push(''));
        });
    }, function ($_, $reject) {
        $reject(new RuntimeException('Cancelled buffering'));
    });
    return $promise->then(null, function ($error) use (&$buffer, $bufferer, $stream) {
        // promise rejected => clear buffer and buffering
        $buffer = '';
        $stream->removeListener('data', $bufferer);
        throw $error;
    });
}