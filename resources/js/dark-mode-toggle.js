/*
    Dark Mode Switcher: https://github.com/ditdot-dev/dark-mode-example
    Image Mode Switcher: https://michaelti.ca/sandbox/2020/05/01/dark-mode-images-with-a-manual-toggle-switch/
 */
let darkModeState = false;

const button = document.querySelector(".theme-toggle");

// MediaQueryList object
const useDark = window.matchMedia("(prefers-color-scheme: dark)");

// Toggles the "dark-mode" class
function toggleDarkMode(state)
{
    document.documentElement.classList.toggle("dark-mode", state);
    document.documentElement.classList.toggle("light-mode", !state);
    setPicturesThemed(state ? 'dark' : 'light');
    darkModeState = state;
}

// Sets localStorage state
function setDarkModeLocalStorage(state)
{
    localStorage.setItem("dark-mode", state);
}

function setPicturesThemed(colorScheme)
{
    // Clean up all existing picture sources that were cloned
    document.querySelectorAll("picture > source[data-cloned-theme]").forEach((el) => {
        el.remove();
    });

    if (colorScheme) {
        // Find all picture sources with the desired colour scheme
        document
            .querySelectorAll(`picture > source[media*="(prefers-color-scheme: ${colorScheme})"]`)
            .forEach((el) => {
                // 1. Clone the given <source>
                // 2. Remove the media attribute so the new <source> is unconditional
                // 3. Add a "data-cloned-theme" attribute to it for future reference / removal
                // 4. Prepend the new <source> to the parent <picture> so it takes precedence
                const cloned = el.cloneNode();
                cloned.removeAttribute("media");
                cloned.setAttribute("data-cloned-theme", colorScheme);
                el.parentNode.prepend(cloned);
            });
    }
}

// Initial setting
toggleDarkMode(localStorage.getItem("dark-mode") == "true");

// Listen for changes in the OS settings.
// Note: the arrow function shorthand works only in modern browsers,
// for older browsers define the function using the function keyword.
//useDark.addListener((evt) => toggleDarkMode(evt.matches));
useDark.addEventListener('change', (evt) => toggleDarkMode(evt.matches))

// Toggles the "dark-mode" class on click and sets localStorage state
button.addEventListener("click", (evt) => {
    evt.preventDefault();
    darkModeState = !darkModeState;

    toggleDarkMode(darkModeState);
    setDarkModeLocalStorage(darkModeState);
});
