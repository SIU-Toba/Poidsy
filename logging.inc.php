<?PHP

/* Poidsy 0.6 - http://chris.smith.name/projects/poidsy
 * Copyright (c) 2008-2010 Chris Smith
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

class Logger {

 const ENABLE_LOGGING = false;
 const LOGGING_FILENAME = '/poidsy-debug.log';
 const TRUNCATE_ARGS = true;
 static $LOGGING_DIRNAME = '/tmp';

 private static $fh;

 public static function setLogDirectory($dirName)
 {
    if (is_dir($dirName) && realpath($dirName) !== false) {
      self::$LOGGING_DIRNAME = $dirName;
    }
 }

 public static function log($message) {
  if (self::ENABLE_LOGGING) {
   if (self::$fh == null) {
    self::$fh = fopen(self::$LOGGING_DIRNAME . self::LOGGING_FILENAME, 'a');
   }

   $args = func_get_args();
   $arg = call_user_func_array('sprintf', $args);
   fputs(self::$fh, sprintf("[%s] %s: %s\n", date('r'), self::getCaller(), $arg));
  }
 }

 protected static function getCaller() {
  $traces = debug_backtrace(); // First one will be getCaller, next Log::logger
  $trace = $traces[2];

  array_walk($trace['args'], array('Logger', 'formatArg'));

  $class = isset($trace['class']) ? $trace['class'] : '';
  $type = isset($trace['type']) ? $trace['type'] : '';

  return sprintf('%s:%s %s%s%s(%s)', basename($trace['file']), $traces[1]['line'], $class, $type, $trace['function'], implode(', ', $trace['args']));
 }

 protected static function formatArg(&$value, $key) {
  if (is_array($value)) {
   $value = '[' . implode(',', $value) . ']';
  } else if (is_object($value)) {
   $value = '(object: ' . get_class($value) . ')';
  }

  if (strlen($value) > 30 && self::TRUNCATE_ARGS) {
   $value = substr($value, 0, 27) . '...';
  }
  $value = str_replace("\n", '  ', $value);
 }

}

?>
