export default {
    name: 'config',
    after: 'store',
    
    initialize: function(container, application) {
        
        application.register('config:main', {
            get: function(name) {
                return name ? application.parameters[name] : application.parameters;
            }
        }, {
            instantiate: false,
            singleton: true
        });

        container.typeInjection('controller', 'config', 'config:main');
        container.typeInjection('route', 'config', 'config:main');
        container.typeInjection('component', 'config', 'config:main');
    }
};