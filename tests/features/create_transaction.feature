Feature:
  In order to transfer money
  As a user
  I want to be able to initiate payment transactions

  Scenario: User initiate a valid payment transaction
    When I set "Content-Type" header to "application/json"
    And I make "POST" json request to "/api/transactions" with content:
    """
    {
      "user_id": 1,
      "details": "Transaction number one",
      "recipient_account": "12345",
      "recipient_name": "John Doe",
      "amount": 20.00,
      "currency": "eur"
    }
    """
    Then the response status code should be 201
    And the response JSON should be a single object
    And the response JSON should have "transaction_id" field
    And the response JSON should have "fee" field with value 2
    And the response JSON should have "status" field with value "created"

#  Scenario: User tries to create invalid transaction

#  Scenario: User exceeds transaction rate limit

#  Scenario: User gets transaction fee volume discount

#  Scenario: User exceeds max allowed transfer limit for USD



