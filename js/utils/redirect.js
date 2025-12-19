/**
 * @typedef {Object} DATA
 * @property {Number} countdown
 * @property {String} redirect_url
 */

class Redirect
{
    /** @type {HTMLSpanElement} */
    #_countdown_cont = document.getElementById('countdown');
    #_countdown = parseInt(app.input.countdown);
    
    /** @type {String} */
    #_redirect_url = app.input.redirect_url;
    
    /** @type {Number?} */
    #_interval = null;

    constructor()
    {
        this.#_interval = setInterval(() => this.#updateCountdown(), 1000);
    }

    #updateCountdown()
    {
        this.#_countdown -= 1;
        this.#_countdown_cont.innerText = this.#_countdown.toString();

        if (this.#_countdown <= 0)
        {
            clearInterval(this.#_interval);
            document.location.href = this.#_redirect_url;
        }
    }
}

const redirect = new Redirect();