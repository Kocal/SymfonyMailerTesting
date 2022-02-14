const { isMessageEvent } = require('../../../src/Bridge/JavaScript');

describe('javaScript bridge', function () {
  describe('isMessageEvent()', function () {
    it('checks if passed argument is a valid MessageEvent', function () {
      expect.assertions(6);

      expect(isMessageEvent()).toBeFalsy();
      expect(isMessageEvent('foo')).toBeFalsy();
      expect(isMessageEvent(123)).toBeFalsy();
      expect(isMessageEvent(null)).toBeFalsy();
      expect(isMessageEvent({})).toBeFalsy();
      expect(
        isMessageEvent({
          message: null,
          transport: null,
          queued: null,
        })
      ).toBeTruthy();
    });
  });
});
