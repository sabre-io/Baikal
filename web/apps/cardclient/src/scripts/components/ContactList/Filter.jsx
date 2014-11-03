/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons'),
    _ = require('underscore');

var ContactListFilter = React.createClass({
    render: function() {

        var self = this;
        var change = function(event) {
            self.props.filtered(event.target.value);
        };

        return (
            <div className="component-contact-filter">
                <i className="fa fa-search"></i><input type="text" placeholder="Filter ..." onChange={change} />
            </div>
        );
    }

})

module.exports = ContactListFilter;