classDiagram
    class User {
        +id: bigint
        +name: string
        +email: string
        +password: string
        +phone: string
        +position: string
        +organization_id: bigint
        +profile_completed: boolean
        +created_at: timestamp
        +updated_at: timestamp
        +organization()
        +meetings()
        +questions()
        +hasRole()
    }

    class Organization {
        +id: bigint
        +name: string
        +type: enum[issuer, investor]
        +organization_type: string
        +country: string
        +description: text
        +created_at: timestamp
        +updated_at: timestamp
        +user()
        +meetings()
    }

    class Meeting {
        +id: bigint
        +room_id: bigint
        +start_time: datetime
        +end_time: datetime
        +is_one_on_one: boolean
        +created_at: timestamp
        +updated_at: timestamp
        +room()
        +attendees()
        +questions()
    }

    class MeetingAttendee {
        +id: bigint
        +meeting_id: bigint
        +user_id: bigint
        +role: enum[issuer, investor]
        +created_at: timestamp
        +updated_at: timestamp
        +meeting()
        +user()
    }

    class Question {
        +id: bigint
        +meeting_id: bigint
        +asked_by_id: bigint
        +question: text
        +is_answered: boolean
        +created_at: timestamp
        +updated_at: timestamp
        +meeting()
        +askedBy()
    }

    class Room {
        +id: bigint
        +name: string
        +capacity: integer
        +location: string
        +created_at: timestamp
        +updated_at: timestamp
        +meetings()
    }

    class Role {
        +id: bigint
        +name: string
        +guard_name: string
        +created_at: timestamp
        +updated_at: timestamp
        +permissions()
        +users()
    }

    User "1" -- "1" Organization : has
    Meeting "1" -- "many" MeetingAttendee : has
    User "1" -- "many" MeetingAttendee : participates in
    Room "1" -- "many" Meeting : hosts
    Meeting "1" -- "many" Question : has
    User "1" -- "many" Question : asks
    User "many" -- "many" Role : has
