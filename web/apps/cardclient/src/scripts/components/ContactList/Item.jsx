/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons');

var ContactListItem = React.createClass({
    render: function() {

        var contact = this.props.contact;
        var clicked = this.props.clicked.bind(null, contact);

        var classnames = "component-contact-list-item";
        if(this.props.selected) {
            classnames += ' selected';
        }

        return (
            <div className={classnames} onClick={clicked}>
                {contact.getDisplayName()}
            </div>
        );
    }

})

module.exports = ContactListItem;