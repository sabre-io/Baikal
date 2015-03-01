/*
 * AppDispatcher
 *
 * A singleton that operates as the central hub for application updates.
 */

AppDispatcher = new (require('flux').Dispatcher)(); 

module.exports = AppDispatcher;