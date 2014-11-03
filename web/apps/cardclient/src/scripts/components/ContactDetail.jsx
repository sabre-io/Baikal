/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons'),
    moment = require('moment'),
    EmailList = require('./ContactDetail/Email/List'),
    PhoneList = require('./ContactDetail/Phone/List'),
    URLList = require('./ContactDetail/URL/List'),
    NoteList = require('./ContactDetail/Note/List'),
    AddressList = require('./ContactDetail/Address/List'),
    IconLabel = require('./Common/Indented/IconLabel'),
    Avatar = require('./Common/Avatar');

var ContactDetail = React.createClass({
    render: function() {

        if(!this.props.contact) {
            return (
                <div className="contact-detail">
                    <p>No contact selected.</p>
                </div>
            );
        }

        var contact = this.props.contact;
        var org = contact.get('org');
        var jobtitle = contact.get('title');
        var bday = contact.get('bday');
        var emails = contact.get('email');
        var phones = contact.get('tel');
        var urls = contact.get('url');
        var notes = contact.get('note');
        var addresses = contact.get('adr');
        var photo = contact.get('photo');

        if(bday && bday.trim()) {
            try {
                bday = moment(bday).format('MMMM Do, YYYY');
            } catch(e) { }
        }

        var displayname = contact.getDisplayName();
        var company = contact.company();
        var companyname = contact.getCompanyName();
        var department = contact.getDepartment();
        var contactname = contact.getContactName();

        var subname = null;
        var subname2 = null;
        var jobline = null;

        if(jobtitle) {
            if(department) {
                jobline = (<div>{jobtitle} <span>&mdash;</span> {department}</div>);
            } else {
                jobline = (<div>{jobtitle}</div>);
            }
        } else if(department) {
            jobline = (<div>{department}</div>);
        }
        
        if(company) {

            if(jobline) {
                subname = (<IconLabel className="job" icon="briefcase">
                    {jobline && (<div className="jobtitle">{jobline}</div>)}
                </IconLabel>);
            }

            if(contactname) {
                subname2 = (<IconLabel className="individual" icon="user">
                    {contactname && (<div className="contactname">{contactname}</div>)}
                </IconLabel>);
            }

        } else {

            if(jobline || companyname) {
                subname = (<IconLabel className="job" icon="briefcase">
                    {jobline && (<div className="jobtitle">{jobline}</div>)}
                    {companyname && (<div className="organization">{companyname}</div>)}
                </IconLabel>);
            }
        }

        return (
            <div className="contact-detail">

                <div className="header clearfix">
                    <div className="avatar">
                        <Avatar photo={photo} caps={contact.getCaps()} />
                    </div>
                    <div className="identity">
                        <div className="fullname">{contact.getDisplayName()}</div>

                        {subname}
                        {subname2}

                        {bday &&
                            <IconLabel className="birthday" icon="birthday-cake">
                                <span>{bday}</span>
                            </IconLabel>
                        }
                    </div>
                </div>

                {phones && <PhoneList phones={phones} />}

                {emails && <EmailList emails={emails} />}

                {addresses && <AddressList addresses={addresses} />}

                {urls && <URLList urls={urls} />}

                {notes && <NoteList notes={notes} />}

            </div>
        );
    }

})

module.exports = ContactDetail;