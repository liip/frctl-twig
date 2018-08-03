'use strict';

const _ = require('lodash');
const twig = require('./engine.js').renderFile;
const Adapter = require('@frctl/fractal').Adapter;
const utils      = require('@frctl/fractal').utils;

class TwigAdapter extends Adapter {

  constructor(source, app, twigOptions) {
    super(null, source);
    this._app = app;

    if (typeof twigOptions === 'undefined') {
      twigOptions = {};
    }

    this._twigOptions = twigOptions;
  }

  render(path, str, context, meta) {
    const partials = {};
    const self = this;
    meta = meta || {};

    setEnv('_self', meta.self, context);
    setEnv('_target', meta.target, context);
    setEnv('_env', meta.env, context);

    _.each(this._views, function (view) {
      if (path == view.path) return;
      partials[view.handle] = view.path;
    });

    var options = {
      context: context,
      aliases: partials,
      root: this._source.fullPath,
      staticRoot: !meta.env.request && !meta._request
        ? '/'
        : utils.relUrlPath(
            '/file',
            _.get(meta.env.request || meta._request, 'path', '/'),
            { ext: '' }
          ).replace('/file', '/')
    };

    var mergedOptions = Object.assign({}, this._twigOptions, options);

    return new Promise(function (res, rej) {
      twig(path, mergedOptions, function (err, html) {
        err ? rej(err) : res(html);
      });
    });
  }

}

function setEnv(key, value, context) {
  if (_.isUndefined(context[key]) && !_.isUndefined(value)) {
    context[key] = value;
  }
}

module.exports = function (twigOptions) {

  return {
    register(source, app) {
      return new TwigAdapter(source, app, twigOptions);
    }
  }

};
