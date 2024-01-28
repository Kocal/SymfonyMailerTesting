const { isMessageEvent } = require('../../../src/Bridge/JavaScript');

describe('javaScript bridge', function () {
  describe('isMessageEvent()', function () {
    it('checks if passed argument is not a valid MessageEvent', function () {
      expect.assertions(5);

      expect(isMessageEvent()).toBeFalsy();
      expect(isMessageEvent('foo')).toBeFalsy();
      expect(isMessageEvent(123)).toBeFalsy();
      expect(isMessageEvent(null)).toBeFalsy();
      expect(isMessageEvent({})).toBeFalsy();
    });

    it('checks if passed argument is a valid MessageEvent', function () {
      expect.assertions(1);

      expect(
        isMessageEvent({
          message: null,
          transport: null,
          queued: null,
        }),
      ).toBeTruthy();
    });
  });
});
