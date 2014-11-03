/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons'),
    Section = require('../Section'),
    EmailItem = require('./EmailItem');

var ContactDetailEmailList = React.createClass({
    render: function() {
        var emails = this.props.emails;

        return (
            <Section title="Emails">
                {emails.map(function(email) {
                    return (<EmailItem key={email.value} email={email} />);
                })}
            </Section>
        );
    }

})

module.exports = ContactDetailEmailList;