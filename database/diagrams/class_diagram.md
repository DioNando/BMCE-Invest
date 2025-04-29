classDiagram
    class User {
        +id: bigint
        +name: string
        +email: string
        +password: string
        +phone: string
        +position: string
        +organization_id: bigint
        +status: boolean
        +created_at: timestamp
        +updated_at: timestamp
        +organization()
        +timeSlots()
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
        +timeSlots()
    }

    class TimeSlot {
        +id: bigint
        +room_id: bigint
        +created_by_id: bigint
        +start_time: datetime
        +end_time: datetime
        +is_one_on_one: boolean
        +availability: boolean
        +created_at: timestamp
        +updated_at: timestamp
        +room()
        +attendees()
        +questions()
    }

    class TimeSlotAttendee {
        +id: bigint
        +time_slot_id: bigint
        +user_id: bigint
        +role: enum[issuer, investor]
        +created_at: timestamp
        +updated_at: timestamp
        +timeSlot()
        +user()
    }

    class Question {
        +id: bigint
        +time_slot_id: bigint
        +user_id: bigint
        +question: text
        +is_answered: boolean
        +created_at: timestamp
        +updated_at: timestamp
        +timeSlot()
        +askedBy()
    }

    class Room {
        +id: bigint
        +name: string
        +capacity: integer
        +location: string
        +created_at: timestamp
        +updated_at: timestamp
        +timeSlots()
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
    TimeSlot "1" -- "many" TimeSlotAttendee : has
    User "1" -- "many" TimeSlotAttendee : participates in
    Room "1" -- "many" TimeSlot : hosts
    TimeSlot "1" -- "many" Question : has
    User "1" -- "many" Question : asks
    User "many" -- "many" Role : has
