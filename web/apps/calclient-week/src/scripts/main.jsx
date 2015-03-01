'use strict';

try {
    var config = JSON.parse(unescape(document.getElementsByName('config/environment')[0]['content']));
} catch(e) {
    var config = {
        rootElement: '#content',
        parameters: {
            apiendpoint: '',
            calendarsEnabledAtStart: null,
            calendarFocusedAtStart: null,
            starttime: 0,
            endtime: 23,
            businessstarttime: 7,
            businessendtime: 20,
            hourbarwidth: 30,
            hourheight: 60,
            allowMovingToAdjacentWeeks: true,
            windowed: true,
            cosmetic: {
                gutterwidth: 32,
                event: {
                    marginLeft: 2,
                    marginTop: 2,
                    marginBottom: 3
                },
                popup: {
                    width: 300,
                    height: 170
                }
            }
        }
    }
}

if(config) {

    // shiming Object.assign (required for JSX {...this.props} in browsers)
    require('object.assign').shim();

    var App = require('./components/App.react'),
        Calendar = require('./components/Calendar.react'),
        Edit = require('./components/Edit.react'),
        React = require('react'),
        Router = require('react-router'),
        Route = Router.Route,
        DefaultRoute = Router.DefaultRoute;

    // Export React so the devtools can find it
    (window !== window.top ? window.top : window).React = React;

    require('../styles/main.css');
    require('../../node_modules/nprogress/nprogress.css');

    var routes = (
        <Route name="calendar" path="/" handler={App}>
            <Route name="edit" path="/edit/:eventid" handler={Edit} />
            <DefaultRoute handler={Calendar} />
        </Route>
    );

    Router.run(routes, Router.HashLocation, function (Handler) {
        React.render(
            <Handler
                apiendpoint={config.parameters.apiendpoint}
                calendarsEnabledAtStart={config.parameters.calendarsEnabledAtStart}
                calendarFocusedAtStart={config.parameters.calendarsEnabledAtStart}
                starttime={config.parameters.starttime}
                endtime={config.parameters.endtime}
                businessstarttime={config.parameters.businessstarttime}
                businessendtime={config.parameters.businessendtime}
                hourbarwidth={config.parameters.hourbarwidth}
                hourheight={config.parameters.hourheight}
                allowMovingToAdjacentWeeks={config.parameters.allowMovingToAdjacentWeeks}
                windowed={config.parameters.windowed}
                cosmetic={config.parameters.cosmetic} />,
            $(config.rootElement).get(0)
        );
    });
}