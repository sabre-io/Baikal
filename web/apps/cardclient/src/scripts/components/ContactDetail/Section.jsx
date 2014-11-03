/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons');

var ContactDetailSection = React.createClass({
    render: function() {
        
        var classname = 'section section-' + this.props.title.toLowerCase().trim().replace(/\s/, '-').replace(/[^a-z\-]/, '');
        return (
            <div className={classname}>
                {/*<h3>{this.props.title}</h3>*/}
                {this.props.children}
            </div>
        );
    }

})

module.exports = ContactDetailSection;