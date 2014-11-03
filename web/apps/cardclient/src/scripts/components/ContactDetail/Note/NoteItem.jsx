/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons'),
    TextLabel = require('../../Common/Indented/TextLabel');

var ContactDetailNoteListItem = React.createClass({
    render: function() {

        return (
            <TextLabel label="note">
                <div style={{'white-space': 'pre-wrap'}}>{this.props.note.trim()}</div>
            </TextLabel>
        );
    }

})

module.exports = ContactDetailNoteListItem;