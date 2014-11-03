/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons'),
    keymaster = require('keymaster');

// Export React so the devtools can find it
(window !== window.top ? window.top : window).React = React;

// CSS
require('../../styles/main.css');

var App = React.createClass({
    render: function() {
        return (
            <div className='cardclient'>
                <this.props.activeRouteHandler/>
            </div>
        );
    }
});

module.exports = App;
