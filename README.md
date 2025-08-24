# Table Game
It's a Table Game web app 



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
    - `invitation_token` (string, required).
    
- **POST** `/api/user/rooms/{id}/leave` - Leave a room by ID.


### Game

**1. Get Current Game State**

Retrieve the current state of the game for the authenticated user.

- **URL**: `/api/user/game`
    
- **Method**: `GET`


**2. Handle Player Action**

Process a player's action during the game.

- **URL**: `/api/user/game/action`
    
- **Method**: `POST`
    
- **Request Body**:
    
    - `game_data` (JSON, required): A JSON object containing the action details.
        
        - `selectedPairIndex` (integer, required): Index of the selected card pair (0-2).
            
        - `selectedHouses` (array, required): Array of selected house IDs (1-2 items).
            
        - `agentNumber` (integer, optional): Number of agents (-2 to 2).
            
        - `estateIndex` (integer, optional): Index of the estate.
            
        - `fenceId` (integer, optional): ID of the selected fence.
            
        - `action` (integer, required): The action type.
            
        - `number` (integer, required): A number associated with the action.


**3. Skip Player Turn**

Skip the current player's turn.

- **URL**: `/api/user/game/skip`
    
- **Method**: `POST`


**4. Get Game End State**

Retrieve the final state of the game after it has ended.

- **URL**: `/api/user/game/{room_id}/end`
    
- **Method**: `GET`
