declare namespace SymfonyMailerTesting {
  // the following types should match the return structures of the following PHP normalizers:
  // - src/Normalizer/MessageEventsNormalizer.php
  // - src/Normalizer/MessageEventNormalizer.php
  // - src/Normalizer/RawMessageNormalizer.php
  // - src/Normalizer/MessageNormalizer.php
  // - src/Normalizer/EmailNormalizer.php

  interface MessageEvents {
    events: Array<MessageEvent>;
    transports: Array<Transport>
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
    headers: string[];
    body: null | {
      headers: string[];
      body: string;
    };
  }

  interface Email {
    headers: string[];
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
    attachments: string[]
  }

  interface Address {
    address: string;
    name: string;
  }
}
