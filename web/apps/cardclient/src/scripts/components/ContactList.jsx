/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons'),
    ContactListItem = require('./ContactList/Item');

var _ = require('underscore');

var ContactList = React.createClass({
    getInitialState: function() {
        return {
            windowheight: null
        };
    },
    componentWillMount : function() {
        var self = this;

        // Watching contacts
        this.props.contacts.on('change reset add remove', _.debounce(function() {
            var datebefore = new Date();
            self.forceUpdate(function() {
                var dateafter = new Date();
                console.log('Re-rendered ContactList, took', dateafter - datebefore, 'ms');
            });
        }, 1, true));

        // Initializing dimensions
        this.updateDimensions();
    },
    updateDimensions: _.debounce(function() {
        this.setState({windowheight: $(window).height()});
    }, 16),
    componentDidMount: function() {
        // Watching window dimensions
        window.addEventListener("resize", this.updateDimensions);
    },
    componentWillUnmount : function() {
        // Unwatching contacts
        this.props.contacts.off('change reset add remove');

        // Unwatching window dimensions
        window.removeEventListener("resize", this.updateDimensions);
    },
    render: function() {
        var self = this;
        return (
            <div className="component-contactlist" style={{height: (this.state.windowheight - 250) + 'px'}}>
                {this.props.contacts.map(function(contact) {
                    var selected = (self.props.selected && self.props.selected.get('id') === contact.get('id'));
                    return <ContactListItem key={contact.get('id')} contact={contact} clicked={self.props.clicked} selected={selected} />;
                })}
            </div>
        );
    }

})

module.exports = ContactList;