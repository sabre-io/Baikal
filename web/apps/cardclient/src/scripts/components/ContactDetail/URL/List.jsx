/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons'),
    Section = require('../Section'),
    URLItem = require('./URLItem');

var ContactDetailURLList = React.createClass({
    render: function() {
        var urls = this.props.urls;

        return (
            <Section title="Websites">
                {urls.map(function(url) {
                    return (<URLItem key={url.value} url={url} />);
                })}
            </Section>
        );
    }

})

module.exports = ContactDetailURLList;