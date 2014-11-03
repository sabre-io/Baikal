/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons'),
    Section = require('../Section'),
    NoteItem = require('./NoteItem');

var ContactDetailNoteList = React.createClass({
    render: function() {
        var notes = this.props.notes;

        return (
            <Section title="Notes">
                {notes.map(function(note, key) {
                    return (<NoteItem key={key} note={note} />);
                })}
            </Section>
        );
    }

})

module.exports = ContactDetailNoteList;