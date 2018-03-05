const execPHP = require('exec-php');
const trim = require('trim');

const twigOptions = {
  root: null,
  context: {}
};

exports.renderFile = function (entry, options, cb) {
  // Merge the global options with the local ones.
  options = Object.assign({}, twigOptions, options);

  execPHP('engine.php', null, function (error, php) {
    // Call the callback on error or the render function on success.
    error ? cb(error) : php.render(entry, options, function (error, stdout) {
      // Call the callback with an error or the trimmed output.
      error ? cb(error) : cb(null, trim(stdout));
    });
  });
};

exports.createEngine = function (options) {
  // Merge the options with default options.
  twigOptions = Object.assign(twigOptions, options);

  return exports.renderFile;
};

exports.__express = exports.renderFile;
