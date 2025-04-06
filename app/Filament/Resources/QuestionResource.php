<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers;
use App\Models\Question;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\HasManyRepeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $label = 'Голосование';
    protected static ?string $pluralLabel = 'Голосования';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Тема')
                    ->required(),
                Textarea::make('description')
                    ->label('Краткое описание')
                    ->required(),
                Select::make('voting_type')
                    ->label('Тип голосования')
                    ->options(['com' => 'Комитет', 'bod' => 'Совет директоров'])
                    ->required(),
                Select::make('voting_way')
                    ->label('Способ голосования')
                    ->options(['majority' => 'Кворум', 'unanimous' => 'Единогласное'])
                    ->required(),
                DateTimePicker::make('end_date')
                    ->label('Дата и время окончания голосования')
                    ->required(),
                TextInput::make('conference_link')
                    ->label('Ссылка на онлайн-конференцию'),

                Select::make('department_id')
                    ->label('Подразделение')
                    ->relationship('department', 'name')
                    ->preload()
                    ->searchable()
                    ->required(),

                // 👇 Inline файлы
                HasManyRepeater::make('files')
                    ->relationship('files')
                    ->label('Файлы')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->label('Имя файла'),
                        FileUpload::make('path')
                            ->directory('voting-files')
                            ->label('Файл')
                            ->required()
                            ->preserveFilenames()
                    ])
                    ->createItemButtonLabel('Добавить файл')
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Тема')
                    ->searchable(),
                TextColumn::make('voting_type')
                    ->label('Тип голосования')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'bod' => 'Совет директоров',
                            'com' => 'Комитет',
                            default => 'Неизвестно',
                        };
                    }),
                TextColumn::make('voting_way')
                    ->label('Способ голосования')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'majority' => 'Кворум',
                            'unanimous' => 'Единогласное',
                            default => 'Неизвестно',
                        };
                    }),
                TextColumn::make('end_date')
                    ->label('Дата окончания')
                    ->dateTime(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
