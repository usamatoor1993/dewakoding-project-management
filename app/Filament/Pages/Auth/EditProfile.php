<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    protected static ?string $title = 'My Profile';

    public function getSubheading(): string
    {
        return 'Update your personal information and password';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->description('Update your avatar and personal details')
                    ->icon('heroicon-m-user')
                    ->schema([
                        $this->getAvatarFormComponent(),
                        Grid::make(2)
                            ->schema([
                                $this->getNameFormComponent(),
                                $this->getEmailFormComponent(),
                                $this->getJobTitleFormComponent(),
                                $this->getPhoneFormComponent(),
                            ]),
                        $this->getBioFormComponent(),
                    ]),

                Section::make('Change Password')
                    ->description('Leave blank if you do not want to change your password')
                    ->icon('heroicon-m-key')
                    ->schema([
                        $this->getCurrentPasswordFormComponent(),
                        Grid::make(2)
                            ->schema([
                                $this->getPasswordFormComponent(),
                                $this->getPasswordConfirmationFormComponent(),
                            ]),
                    ]),
            ]);
    }

    protected function getAvatarFormComponent(): Component
    {
        return FileUpload::make('avatar')
            ->label('Profile Photo')
            ->image()
            ->avatar()
            ->imageEditor()
            ->directory('avatars')
            ->disk('public')
            ->visibility('public')
            ->imageResizeMode('cover')
            ->imageCropAspectRatio('1:1')
            ->imageResizeTargetWidth('300')
            ->imageResizeTargetHeight('300');
    }

    protected function getJobTitleFormComponent(): Component
    {
        return TextInput::make('job_title')
            ->label('Job Title')
            ->placeholder('e.g. Senior Developer')
            ->maxLength(255);
    }

    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone')
            ->label('Phone Number')
            ->tel()
            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
            ->placeholder('e.g. +628123456789')
            ->maxLength(255);
    }

    protected function getBioFormComponent(): Component
    {
        return Textarea::make('bio')
            ->label('Bio')
            ->placeholder('Tell us a little about yourself...')
            ->rows(4)
            ->maxLength(1000)
            ->columnSpanFull();
    }
}