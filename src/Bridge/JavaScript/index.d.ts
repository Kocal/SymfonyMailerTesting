declare function filterMessageEvents(
  messageEvents: SymfonyMailerTesting.MessageEvents,
  { transport: string, queued: bool },
);

declare function filterAttachments(messageEvent: SymfonyMailerTesting.MessageEvent, { name: string });

declare function filterHeader(messageEvent: SymfonyMailerTesting.MessageEvent, { name: string });

declare function isMessageEvent(messageEvent: SymfonyMailerTesting.MessageEvent);

declare namespace SymfonyMailerTesting {
  // the following types should match the return structures of the following PHP normalizers:
  // - src/Normalizer/MessageEventsNormalizer.php
  // - src/Normalizer/MessageEventNormalizer.php
  // - src/Normalizer/RawMessageNormalizer.php
  // - src/Normalizer/MessageNormalizer.php
  // - src/Normalizer/EmailNormalizer.php

  interface MessageEvents {
    events: Array<MessageEvent>;
    transports: Array<Transport>;
  }

  interface MessageEvent {
    message: RawMessage | Message | Email;
    transport: Transport;
    queued: boolean;
  }

  type Transport = string;

  interface RawMessage {
    message: string;
  }

  interface Message {
    headers: Array<Header>;
    body: null | {
      headers: Array<Header>;
      body: string;
    };
  }

  interface Email {
    headers: Array<Header>;
    from: Array<Address>;
    to: Array<Address>;
    cc: Array<Address>;
    bcc: Array<Address>;
    subject: string;
    text: {
      body: string;
      charset: string;
    };
    html: {
      body: string;
      charset: string;
    };
    attachments: string[];
  }

  interface Header {
    name: string;
    body: string;
  }

  interface Address {
    address: string;
    name: string;
  }
}
