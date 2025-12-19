/** 
 * @typedef {Object} App
 * @property {DATA} input
 */

class App
{
    #_input = {};

    constructor()
    {
        if (typeof window.APP_INPUT !== 'undefined')
        {
            this.#_input = window.APP_INPUT;
        }
    }

    /** @returns {DATA} */
    get input()
    {
        return this.#_input;
    }
}

// Just define app variable for type checking
if (false)
{
    /** @type {App} */
    var app;
}