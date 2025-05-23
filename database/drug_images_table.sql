-- Thêm bảng DrugImages vào database hiện có
USE CommunityHealthDB;
GO

-- Tạo bảng DrugImages để lưu thông tin về các ảnh minh họa ma túy
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'DrugImages')
BEGIN
    CREATE TABLE DrugImages (
        ImageID INT IDENTITY(1,1) PRIMARY KEY,
        DrugID INT NOT NULL,
        ImageURL NVARCHAR(500) NOT NULL,
        Caption NVARCHAR(255),
        CreatedAt DATETIME NOT NULL DEFAULT GETDATE(),
        FOREIGN KEY (DrugID) REFERENCES Drugs(DrugID) ON DELETE CASCADE
    );
END
GO

-- Tạo index để tìm kiếm ảnh theo DrugID
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_DrugImages_DrugID')
BEGIN
    CREATE INDEX IX_DrugImages_DrugID ON DrugImages(DrugID);
END
GO 