'use strict';

const _ = require('lodash');
const twig = require('./engine.js').renderFile;
const Adapter = require('@frctl/fractal').Adapter;
const utils      = require('@frctl/fractal').utils;

class TwigAdapter extends Adapter {

  constructor(source, app) {
    super(null, source);
    this._app = app;
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

    const options = {
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

    return new Promise(function (res, rej) {
      twig(path, options, function (err, html) {
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

module.exports = function () {

  return {
    register(source, app) {
      return new TwigAdapter(source, app);
    }
  }

};
