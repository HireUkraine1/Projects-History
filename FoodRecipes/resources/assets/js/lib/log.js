/**
 * Console log helper
 */
if (typeof log !== 'function') {
    function log(val) {
        try {
            console.log(val);
        } catch (e) {
        }
    }
}