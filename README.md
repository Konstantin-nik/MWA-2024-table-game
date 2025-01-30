# Table Game
It's a Table Game web app for Modern Web Application 1 course 2024-25



## API Documentation: 
### Base URL
```
/api/user
```
### Authentication
All endpoints require user token that you can get at http://tablegame.ip-ddns.com/profile

### Room Management

#### Endpoints
**1. List All Public Rooms** 

Retrieve a list of all public rooms available to join.

- URL: `/api/user/rooms`
- Method: `GET`

**2. Create a New Room** 

Create a new room. The authenticated user will automatically join the room as the owner.

- URL: `/api/user/rooms`
- Method: `POST`
- Request Body:
    - `name` (string, required): The name of the room (4-25 characters).
    - `capacity` (integer, required): The maximum number of players (2-8).
    - `is_public` (boolean, optional): Whether the room is public (default: false).

**3. Get Room Details**

Retrieve details of a specific room.

- **URL**: `/api/user/rooms/{id}`
- **Method**: `GET`


**4. Update Room**

Update the details of a room. Only the room owner can update the room.

- **URL**: `/api/user/rooms/{id}`
    
- **Method**: `PUT`
    
- **Request Body**:
    
    - `name` (string, optional): The new name of the room (4-25 characters).
        
    - `capacity` (integer, optional): The new capacity of the room (2-8).
        
    - `is_public` (boolean, optional): Whether the room is public.

**5. Delete Room**

Delete a room. Only the room owner can delete the room.

- **URL**: `/api/user/rooms/{id}`
    
- **Method**: `DELETE`


**6. List Owned Rooms**

Retrieve a list of rooms owned by the authenticated user.

- **URL**: `/api/user/owned_rooms`
    
- **Method**: `GET`


**7. Start Game**

Start the game for a specific room. Only the room owner can start the game.

- **URL**: `/api/user/rooms/{id}/start`
    
- **Method**: `POST`



### Participation

- **GET** `/api/user/participations` - List all participations for the authenticated user.

### Join Room

- **POST** `/api/user/rooms/{id}/join` - Join a room by ID.
    
- **POST** `/api/user/rooms/join-by-token` - Join a room by invitation token.
    
- **POST** `/api/user/rooms/{id}/leave` - Leave a room by ID.


### Game

- **GET** `/api/user/game` - Get the current game state.
    
- **POST** `/api/user/game/action` - Handle a player action.
    
- **POST** `/api/user/game/skip` - Handle a player skipping their turn.
    
- **GET** `/api/user/game/{room_id}/end` - Get the game end state.