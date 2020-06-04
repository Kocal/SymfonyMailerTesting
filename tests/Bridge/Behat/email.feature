Feature: Testing emails

  Scenario: I can test how many emails have been sent
    When I send an email:
      | from    | john@example.com  |
      | to      | carla@example.com |
      | subject | Hello             |
      | text    | Hi Carla!         |
    Then 1 email should have been sent
    And 1 email should have been sent in transport "null://"

  Scenario: I can test if emails are queued or not
    When I send an email:
      | from    | john@example.com  |
      | to      | carla@example.com |
      | subject | Hello             |
      | text    | Hi Carla!         |

    Then I select email #0
    And this email is not queued

    Then I select email #0 from transport "null://"
    And this email is not queued

  Scenario: I can test emails attachments
    When I send an email:
      | from        | john@example.com                                      |
      | to          | carla@example.com                                     |
      | subject     | Hello                                                 |
      | text        | Hi Carla!                                             |
      | attachments | [{"body": "My attachment", "name": "attachment.txt"}] |

    Then I select email #0
    And this email has 1 attachment
    And this email has attachment named "attachment.txt"
    And this email has attachment name matching "#^attachment#"

  Scenario: I can test if emails text body contains
    When I send an email:
      | from    | john@example.com  |
      | to      | carla@example.com |
      | subject | Hello             |
      | text    | Hi Carla!         |

    Then I select email #0
    And this email text body contains "Hi Carla!"
    And this email text body not contains "Bye Carla!"
    And this email text body matches "#Hi [a-zA-Z]+!#"
    And this email text body not matches "#Bye [a-zA-Z]+!#"

  Scenario: I can test if emails HTML body contains
    When I send an email:
      | from    | john@example.com  |
      | to      | carla@example.com |
      | subject | Hello             |
      | html    | <b>Hi Carla!</b>  |

    Then I select email #0
    And this email HTML body contains "<b>Hi Carla!</b>"
    And this email HTML body not contains "Bye Carla!"
    And this email HTML body matches "#Hi [a-zA-Z]+!#"
    And this email HTML body not matches "#Bye [a-zA-Z]+!#"

  Scenario: I can test emails headers
    When I send an email:
      | from    | john@example.com  |
      | to      | carla@example.com |
      | subject | Hello             |
      | text    | Hi Carla!         |

    Then I select email #0
    And this email has header "To"
    And this email header "To" has value "carla@example.com"
    And this email header "To" has not value "john@example.com"
    And this email has no header "Foobar"

  Scenario: I can test emails addresses
    When I send an email:
      | from    | john@example.com  |
      | to      | carla@example.com |
      | subject | Hello             |
      | text    | Hi Carla!         |

    Then I select email #0
    And this email contains address "From" "john@example.com"
    And this email contains address "to" "carla@example.com"

  Scenario: I can test email subject
    When I send an email:
      | from    | john@example.com  |
      | to      | carla@example.com |
      | subject | Hello             |
      | text    | Hi Carla!         |

    Then I select email #0
    And this email subject has value "Hello"
    And this email subject contains "Hell"
    And this email subject matches "/^[a-zA-Z]+$/"
