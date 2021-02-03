# WeeklyBuddy FRONTEND

## Getting started

To use this project [Node](https://nodejs.org/) and [npm](https://www.npmjs.com/) are required. To start locally  this project or build it for production you need to follow several steps :

1. Installing dependencies :  
   `npm i`  
2. Compile it :  
   **For development :**  
   `npm run start`  
   It will launch the application in development mode at `localhost:3001`.  
   **For production :**  
   `npm run build`  
   It will create all needed files in a folder named `dist`, just copy them into your webserver.

## Formatting source code

In order to ensure a global coherence of the code rules, [prettier](https://prettier.io/) is configured on this project. To format source files just use the following command :  
`npm run format`

## FAQ

### CORS header 'Access-Control-Allow-Origin' missing or not matching

If you try to install your own WeeklyBuddy instance or to launch it locally (in development mode) you will need to add your DNS to the authorized DNS list in the backend. Referer to corresponding question in the FAQ section the backend README.
