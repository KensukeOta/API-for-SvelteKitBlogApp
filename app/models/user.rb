class User < ApplicationRecord
  has_many :posts, dependent: :destroy
  
  validates :name, presence: true, length: { maximum: 10 }
  validates :email, presence: true

  has_secure_password
end
