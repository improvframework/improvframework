<?php
/**
 * Copyright (c) 2015, Jim DeLois
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this
 * list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * 3. Neither the name of the copyright holder nor the names of its contributors
 * may be used to endorse or promote products derived from this software without
 * specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author     Jim DeLois <%%PHPDOC_AUTHOR_EMAIL%%>
 * @copyright  2015 Jim DeLois
 * @license    http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version    %%PHPDOC_VERSION%%
 * @link       https://github.com/improvframework/http-constants
 * @filesource
 *
 */

namespace Improv\Http\Response;

/**
 * A utility class to encapsulate HTTP Response Codes and the validation thereof.
 *
 * This class contains a set of common HTTP Response codes, and a utility validator.
 *
 * @author  Jim DeLois <%%PHPDOC_AUTHOR_EMAIL%%>
 * @package Improv\Http\Response
 */
final class Code {

  const CODE_CONTINUE                        = 100;
  const SWITCHING_PROTOCOLS                  = 101;
  const PROCESSING                           = 102;
  const CHECKPOINT                           = 103;

  const OK                                   = 200;
  const CREATED                              = 201;
  const ACCEPTED                             = 202;
  const NON_AUTHORITATIVE_INFORMATION        = 203;
  const NO_CONTENT                           = 204;
  const RESET_CONTENT                        = 205;
  const PARTIAL_CONTENT                      = 206;
  const MULTI_STATUS                         = 207;

  const MULTIPLE_CHOICES                     = 300;
  const MOVED_PERMANENTLY                    = 301;
  const FOUND                                = 302;
  const SEE_OTHER                            = 303;
  const NOT_MODIFIED                         = 304;
  const USE_PROXY                            = 305;
  const SWITCH_PROXY                         = 306;
  const TEMPORARY_REDIRECT                   = 307;
  const RESUME_INCOMPLETE                    = 308;

  const BAD_REQUEST                          = 400;
  const UNAUTHORIZED                         = 401;
  const PAYMENT_REQUIRED                     = 402;
  const FORBIDDEN                            = 403;
  const NOT_FOUND                            = 404;
  const METHOD_NOT_ALLOWED                   = 405;
  const NOT_ACCEPTABLE                       = 406;
  const CONFLICT                             = 409;
  const GONE                                 = 410;
  const LENGTH_REQUIRED                      = 411;
  const PRECONDITION_FAILED                  = 412;
  const REQUEST_ENTITY_TOO_LARGE             = 413;
  const REQUEST_URI_TOO_LONG                 = 414;
  const UNSUPPORTED_MEDIA_TYPE               = 415;
  const EXPECTATION_FAILED                   = 417;
  const IM_A_TEAPOT                          = 418;
  const UNPROCESSABLE_ENTITY                 = 422;
  const LOCKED                               = 423;
  const FAILED_DEPENDENCY                    = 424;
  const UNORDERED_COLLECTION                 = 425;
  const UPGRADE_REQUIRED                     = 426;
  const PRECONDITION_REQUIRED                = 428;
  const TOO_MANY_REQUESTS                    = 429;
  const REQUEST_HEADER_FIELDS_TOO_LARGE      = 431;
  const NO_RESPONSE                          = 444;
  const RETRY_WITH                           = 449;
  const BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS = 450;
  const CLIENT_CLOSED_REQUEST                = 499;

  const INTERNAL_SERVER_ERROR                = 500;
  const NOT_IMPLEMENTED                      = 501;
  const BAD_GATEWAY                          = 502;
  const SERVICE_UNAVAILABLE                  = 503;
  const GATEWAY_TIMEOUT                      = 504;
  const HTTP_VERSION_NOT_SUPPORTED           = 505;
  const VARIANT_ALSO_NEGOTIATES              = 506;
  const INSUFFICIENT_STORAGE                 = 507;
  const BANDWIDTH_LIMIT_EXCEEDED             = 509;
  const NOT_EXTENDED                         = 510;
  const NETWORK_AUTHENTICATION_REQUIRED      = 511;

  /**
   * Validates whether the input response code is defined
   * as a constant in this class.
   *
   * @param integer $code The response code to validate
   *
   * @return bool
   */
  public static function isValid( $code ) {

    static $cache_map = null;

    if ( $cache_map === null ) {
      $class     = new \ReflectionClass( get_called_class() );
      $cache_map = array_values( $class->getConstants() );
    }

    return in_array( $code, $cache_map );

  }

}
