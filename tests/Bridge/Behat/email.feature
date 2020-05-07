Feature: I test an email

  Scenario: Testing email
    When I send an email:
      | to      | hugo@yproximite.com |
      | subject | Foo bar             |
      | body    | Hello world!        |
    Then an email with subject "Foo bar" should have been received
    And this email contains in body "Hello world!"
    Then I delete this email
