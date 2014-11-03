/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons'),
    Section = require('../Section'),
    AddressItem = require('./AddressItem');

var ContactDetailAddressList = React.createClass({
    render: function() {
        var addresses = this.props.addresses;

        return (
            <Section title="Addresses">
                {addresses.map(function(address, key) {
                    return (<AddressItem key={key} address={address} />);
                })}
            </Section>
        );
    }

})

module.exports = ContactDetailAddressList;