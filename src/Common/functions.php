<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Slince\Common;

use Clue\JsonStream\StreamingJsonParser;
use React\Promise;
use React\Stream\ReadableStreamInterface;
use Spike\Common\Exception\RuntimeException;
use Spike\Common\Protocol\HttpHeaderParser;

/**
 * @param ReadableStreamInterface $stream
 * @param callable $resolve
 * @param callable $reject
 * @return void
 */
function jsonBuffer(ReadableStreamInterface $stream, callable $resolve, callable $reject = null)
{
    var_dump('start');
    // stream already ended => resolve with empty buffer
    if (!$stream->isReadable()) {
        return;
    }
    $streamParser = new StreamingJsonParser();
    $bufferer = function ($data) use ($resolve, $streamParser) {
        var_dump('data', $data);
        $parsed = $streamParser->push($data);
        var_dump($parsed);
        if ($parsed) {
            var_dump('parsed');
            $resolve($parsed);
        }
    };
    $stream->on('data', $bufferer);
    $stream->on('error', function ($error) use ($stream, $bufferer, $reject) {
        $stream->removeListener('data', $bufferer);
        $reject && $reject(new RuntimeException('An error occured on the underlying stream while buffering', 0, $error));
    });
    $stream->on('close', function () use ($resolve, $streamParser) {
        $resolve($streamParser->push(''));
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