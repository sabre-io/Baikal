'use strict';

describe('Main', function () {
  var ReactRouterHelloworldApp, component;

  beforeEach(function () {
    var container = document.createElement('div');
    container.id = 'content';
    document.body.appendChild(container);

    ReactRouterHelloworldApp = require('../../../src/scripts/components/ReactRouterHelloworldApp.jsx');
    component = ReactRouterHelloworldApp();
  });

  it('should create a new instance of ReactRouterHelloworldApp', function () {
    expect(component).toBeDefined();
  });
});
