-- Create database if not exists
IF NOT EXISTS (SELECT * FROM sys.databases WHERE name = 'CommunityHealthDB')
BEGIN
    CREATE DATABASE CommunityHealthDB;
END
GO

USE CommunityHealthDB;
GO

-- Create Users table
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'Users')
BEGIN
    CREATE TABLE Users (
        UserID INT IDENTITY(1,1) PRIMARY KEY,
        Email NVARCHAR(255) NOT NULL UNIQUE,
        Password NVARCHAR(255) NOT NULL,
        FullName NVARCHAR(255) NOT NULL,
        Role NVARCHAR(50) NOT NULL DEFAULT 'user',
        CreatedAt DATETIME NOT NULL DEFAULT GETDATE(),
        LastLogin DATETIME NULL,
        IsActive BIT NOT NULL DEFAULT 1
    );
END
GO

-- Create UserSessions table for tracking login sessions
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'UserSessions')
BEGIN
    CREATE TABLE UserSessions (
        SessionID INT IDENTITY(1,1) PRIMARY KEY,
        UserID INT NOT NULL,
        Token NVARCHAR(MAX) NOT NULL,
        CreatedAt DATETIME NOT NULL DEFAULT GETDATE(),
        ExpiresAt DATETIME NOT NULL,
        IsActive BIT NOT NULL DEFAULT 1,
        FOREIGN KEY (UserID) REFERENCES Users(UserID)
    );
END
GO

-- Create stored procedure for user login
IF EXISTS (SELECT * FROM sys.procedures WHERE name = 'sp_UserLogin')
    DROP PROCEDURE sp_UserLogin;
GO

CREATE PROCEDURE sp_UserLogin
    @Email NVARCHAR(255),
    @Password NVARCHAR(255)
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @UserID INT;
    DECLARE @StoredPassword NVARCHAR(255);
    DECLARE @IsActive BIT;
    
    SELECT 
        @UserID = UserID,
        @StoredPassword = Password,
        @IsActive = IsActive
    FROM Users
    WHERE Email = @Email;
    
    IF @UserID IS NULL
        RETURN 1; -- User not found
    
    IF @IsActive = 0
        RETURN 2; -- Account is inactive
    
    -- Update last login time
    UPDATE Users
    SET LastLogin = GETDATE()
    WHERE UserID = @UserID;
    
    RETURN 0; -- Success
END
GO

-- Create stored procedure for user registration
IF EXISTS (SELECT * FROM sys.procedures WHERE name = 'sp_UserRegister')
    DROP PROCEDURE sp_UserRegister;
GO

CREATE PROCEDURE sp_UserRegister
    @Email NVARCHAR(255),
    @Password NVARCHAR(255),
    @FullName NVARCHAR(255)
AS
BEGIN
    SET NOCOUNT ON;
    
    IF EXISTS (SELECT 1 FROM Users WHERE Email = @Email)
        RETURN 1; -- Email already exists
    
    INSERT INTO Users (Email, Password, FullName, Role)
    VALUES (@Email, @Password, @FullName, 'user');
    
    RETURN 0; -- Success
END
GO

-- Create index on Email column for faster lookups
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_Users_Email' AND object_id = OBJECT_ID('Users'))
BEGIN
    CREATE UNIQUE INDEX IX_Users_Email ON Users(Email);
END
GO

-- Create index on UserID in UserSessions for faster lookups
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_UserSessions_UserID' AND object_id = OBJECT_ID('UserSessions'))
BEGIN
    CREATE INDEX IX_UserSessions_UserID ON UserSessions(UserID);
END
GO 