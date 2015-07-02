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

namespace Improv\Http\Request;

/**
 * A utility class to encapsulate HTTP Request Methods and the validation thereof.
 *
 * This class contains a set of common HTTP Request methods, and a utility validator.
 *
 * @author  Jim DeLois <%%PHPDOC_AUTHOR_EMAIL%%>
 * @package Improv\Http\Request
 */
class Method {

  const HEAD    = 'HEAD';
  const OPTIONS = 'OPTIONS';

  const GET     = 'GET';

  const POST    = 'POST';
  const PUT     = 'PUT';
  const PATCH   = 'PATCH';

  const DELETE  = 'DELETE';
  const PURGE   = 'PURGE';

  const TRACE   = 'TRACE';
  const CONNECT = 'CONNECT';

  /**
   * Validates whether the input request method is defined
   * as a constant in this class.
   *
   * @param string $method The request method to validate
   *
   * @return bool
   */
  public static function isValid( $method ) {

    static $cache_map = null;

    if ( $cache_map === null ) {
      $class     = new \ReflectionClass( get_called_class() );
      $cache_map = array_values( $class->getConstants() );
    }

    return in_array( $method, $cache_map );

  }

}
