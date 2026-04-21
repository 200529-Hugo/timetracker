const dtf = require('../src/dateformat');

describe('dateformat', () => {
    test('dformat returns a string', () => {
        expect(typeof dtf.dformat()).toBe('string');
    });

    test('tformat returns a string', () => {
        expect(typeof dtf.tformat()).toBe('string');
    });

    test('dtformat returns a combination of date and time format', () => {
        expect(dtf.dtformat()).toContain(dtf.dformat());
        expect(dtf.dtformat()).toContain(dtf.tformat());
    });

    test('mformat returns a string', () => {
        expect(typeof dtf.mformat()).toBe('string');
    });
});
