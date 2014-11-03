/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons'),
    Section = require('../Section'),
    PhoneItem = require('./PhoneItem');

var ContactDetailPhoneList = React.createClass({
    render: function() {
        var phones = this.props.phones;

        return (
            <Section title="Phones">
                {phones.map(function(phone) {
                    return (<PhoneItem key={phone.value} phone={phone} />);
                })}
            </Section>
        );
    }

})

module.exports = ContactDetailPhoneList;