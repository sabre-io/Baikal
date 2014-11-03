/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons'),
    ContactList = require('./ContactList'),
    ContactListFilter = require('./ContactList/Filter'),
    ContactDetail = require('./ContactDetail'),
    AddressbookCollection = require('../models/AddressbookCollection');

var keymaster = require('keymaster'),
    _ = require('underscore');

keymaster.filter = function(event) {
    var tagName = (event.target || event.srcElement).tagName;
    return !(tagName == 'SELECT' || tagName == 'TEXTAREA');
}

var AddressbookInterface = React.createClass({
    filteredContacts: null,

    getInitialState: function() {
        return {
            contact: null,
            filter: ''
        };
    },
    handleUp: function() {

        if(this.state.contact) {

            var self = this;
            var prev = null;
            
            this.filteredContacts.find(function(item) {
                if(item.get('id') === self.state.contact.get('id')) {
                    return true;
                }

                prev = item;

                return false;
            });
        }

        if(prev) {
            this.setState({contact: prev});
        } else {
            this.setState({contact: this.filteredContacts.last()});
        }

        return false;
    },
    handleDown: function() {

        var following = null;

        if(this.state.contact) {

            var self = this;
            var fetchNext = false;

            var following = this.filteredContacts.find(function(item) {
                if(fetchNext) { return true;}
                fetchNext = (item.get('id') === self.state.contact.get('id'));
            });

            if(following) this.setState({contact: following});
        }

        if(!following && this.filteredContacts.length > 0) {
            this.setState({contact: this.filteredContacts.first()});
        }

        return false;
    },
    componentDidMount: function() {
        this.downHandler = _.debounce(this.handleDown, 1000/60);
        this.upHandler = _.debounce(this.handleUp, 1000/60);
        keymaster('down', this.downHandler);
        keymaster('up', this.upHandler);
    },
    componentWillUnmount: function() {
        keymaster('down', this.downHandler);
        keymaster('up', this.upHandler);
    },
    render: function() {

        var self = this;
        var clicked = function(contact) {
            self.setState({ contact: contact });
        };

        var filtered = function(filter) {
            self.setState({ filter: filter });
        };

        if(this.state.filter.trim() === '') {
            this.filteredContacts = this.props.contacts;
        } else {

            var lowercaseFilter = self.state.filter.toLowerCase();
            var quotedFilter = (function(str) {
                return (str+'').replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1");
            })(lowercaseFilter);
            var search = new RegExp(quotedFilter, 'g');

            this.filteredContacts = new AddressbookCollection(
                this.props.contacts.filter(function(contact) {
                    var contactDigest = (contact.getContactName() + ' ' + contact.getCompanyName()).trim().toLowerCase();

                    return !!contactDigest.match(search);
                })
            );
        }

        return (
            <div className="row">
                <div className="col-xs-4">
                    <ContactListFilter filtered={filtered} />
                    <ContactList contacts={this.filteredContacts} clicked={clicked} selected={this.state.contact}/>
                </div>
                <div className="col-xs-8">
                    <ContactDetail contact={this.state.contact} />
                </div>
            </div>
        );
    }
})

module.exports = AddressbookInterface;