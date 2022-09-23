@extends('core::layouts.app')
@section('title', __('Edit Event'))
@section('content')

    <div class="row mb-4 justify-content-center">
        <div class="col-md-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-2">
                <h1 class="h4 mb-4 text-gray-800 h-max-ellipsis">@lang('Edit event') {{ $event->name }}</h1>
                <div class="ml-auto d-sm-flex">
                    <a href="{{ $event->getPublicUrl() }}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-eye"></i> @lang('Preview')
                    </a>
                </div>
            </div>
            <form id="form_create" method="post" action="{{ route('events.update', ['id' => $event->id]) }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="tab_basic_content" data-toggle="tab"
                           href="#basic_content" role="tab" aria-selected="true"><i
                                    class="fas fa-cog"></i> @lang('Basic')</a>
                        <a class="nav-item nav-link" id="tab_advance_content" data-toggle="tab" href="#advance_content"
                           role="tab" aria-selected="true"><i class="fas fa-sliders-h"></i> @lang('Advance')</a>
                        <a class="nav-item nav-link" id="tab_email_and_notify" data-toggle="tab"
                           href="#email_and_notify" role="tab" aria-selected="true"><i
                                    class="far fa-envelope"></i> @lang('Email & Notify')</a>
                        @if(Module::find('Saas'))
                            <a class="nav-item nav-link" id="tab_domains" data-toggle="tab" href="#nav-domains"
                               role="tab" aria-controls="nav-domains" aria-selected="true"><i
                                        class="fas fa-link"></i> @lang('Custom Domain')</a>
                        @endif
                        <a class="nav-item nav-link" id="tab_seo_config" data-toggle="tab" href="#seo_config" role="tab"
                           aria-selected="true"><i class="fas fa-share-alt"></i> @lang('SEO & Social')</a>
                        <a class="nav-item nav-link" id="tab_theme_design" data-toggle="tab" href="#theme_design"
                           role="tab" aria-selected="true"><i class="fas fa-magic"></i> @lang('Theme Design')</a>
                    </div>
                </nav>
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="basic_content" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Image')</label>
                                            <input name="image" type="file" accept="image/*"><br>
                                            <small>@lang("Image will be displayed in events landing page (best size 200 x 200)")</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        @if(!empty($event->getImage()))
                                            <p><img src="{{$event->getImage()}}"
                                                    data-value="" class="img-thumbnail"
                                                    style="width: 200px; height: 200px;"/></p>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group required">
                                            <label class="form-label">@lang('Name')</label>
                                            <input type="text" name="name" value="{{ old('name', $event->name) }}"
                                                   class="form-control" required/>
                                            @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group required">
                                            <label class="form-label">@lang('Tagline')</label>
                                            <input type="text" name="tagline"
                                                   value="{{ old('tagline', $event->tagline) }}" class="form-control"/>
                                            @error('tagline')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group required">
                                            <label class="form-label">@lang('Maximum Guests')</label>
                                            <input type="number" min="-1" step="1" name="quantity"
                                                   value="{{ old('quantity', $event->quantity) }}"
                                                   class="form-control"/>
                                            <small>@lang('Limit guests register form. Enter -1 for unlimited')</small>
                                            <br>
                                            @error('quantity')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group required">
                                            <label class="form-label">@lang('Register end date')</label>
                                            <div class="input-group date" id="register_end_date"
                                                 data-target-input="nearest">
                                                <input type="text" class="form-control datetimepicker-input"
                                                       name="register_end_date"
                                                       value="{{ old('register_end_date', $event->register_end_date) }}"
                                                       data-target="#register_end_date"/>
                                                <div class="input-group-append" data-target="#register_end_date"
                                                     data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                            <small>@lang('Use to count-down and register form')</small>
                                            <br>
                                            @error('register_end_date')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group required">
                                            <label class="form-label">@lang('Start date')</label>
                                            <div class="input-group date" id="start_date" data-target-input="nearest">
                                                <input type="text" class="form-control datetimepicker-input"
                                                       name="start_date"
                                                       value="{{ old('start_date', $event->start_date) }}"
                                                       data-target="#register_end_date"/>
                                                <div class="input-group-append" data-target="#start_date"
                                                     data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                            <small>@lang('Use to send email and remind event')</small>
                                            <br>
                                            @error('start_date')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group required">
                                            <label class="form-label">@lang('End date')</label> @if($event->eventExpired())
                                                <small class="text-danger">@lang('Event has expired!')</small> @endif
                                            <div class="input-group date" id="end_date" data-target-input="nearest">
                                                <input type="text" class="form-control datetimepicker-input"
                                                       name="end_date"
                                                       value="{{ old('end_date', $event->end_date) }}"
                                                       data-target="#end_date"/>
                                                <div class="input-group-append" data-target="#end_date"
                                                     data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                            <small>@lang('Event end date')</small>
                                            <br>
                                            @error('end_date')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Type')</label>
                                            <div>
                                                <label><input type="radio" name="type"
                                                              value="ONLINE" {{ old('type', $event->type) == 'ONLINE' ? 'checked ' : '' }} /> @lang('Online')
                                                </label>
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <label><input type="radio" name="type"
                                                              value="OFFLINE" {{ old('type', $event->type) == 'OFFLINE' ? 'checked ' : '' }} /> @lang('Offline')
                                                </label>
                                            </div>
                                            @error('type')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Include this event in event listing?(Select no to not have listed with other events)')</label>
                                            <div>
                                                <label><input type="radio" name="is_listing"
                                                              value="1" {{ (int)old('type', $event->is_listing) == 1 ? ' checked ' : '' }} /> @lang('Yes')
                                                </label>
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <label><input type="radio" name="is_listing"
                                                              value="0" {{ (int)old('type', $event->is_listing) == 0 ? ' checked ' : '' }} /> @lang('No')
                                                </label>
                                            </div>
                                            @error('type')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12 address-wrapper">
                                        <div class="form-group required">
                                            <label class="form-label">@lang('Address')</label>
                                            <input type="text" name="address"
                                                   value="{{ old('address', $event->address) }}" class="form-control"/>
                                            @error('address')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group required">
                                            <label class="form-label">@lang('Description')</label>
                                            <textarea name="description" id="description" rows="4"
                                                      class="form-control">{{ old('description', $event->description) }}</textarea>
                                            <small>@lang('Fill instruction, description, image, speakers, roadmap,Q/A...')</small>
                                            <br>
                                            @error('description')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="advance_content" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group required">
                                            <label class="form-label">@lang('Short slug')</label>
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span>{{ route('events.public.show', ['name' => getSlugName(auth()->user()->name),'slug' => '']) }}/</span>
                                                    </div>
                                                </div>
                                                <input type="text" name="short_slug"
                                                       value="{{ old('short_slug', $event->short_slug) }}"
                                                       class="form-control"/>
                                            </div>
                                            @error('short_slug')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">@lang('More infomations')</label>
                                            <small>(@lang('Custom field for form register'))</small>
                                            <template id="info_template">
                                                <tr>
                                                    <td>
                                                        <input type="text" name="info_items[name][]"
                                                               class="form-control" required/>
                                                    </td>
                                                    <td>
                                                        <select name="info_items[data_type][]" class="form-control"
                                                                required>
                                                            <option value="text">@lang('Short text')</option>
                                                            <option value="textarea">@lang('Long text')</option>
                                                            <option value="select">@lang('Dropdown')</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <label class="col-form-label"><input type="checkbox"
                                                                                             name="info_items[is_required][]"
                                                                                             value="1"> </label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="info_items[values][]"
                                                               class="form-control"/>
                                                        <small class="form-text text-muted help-text"></small>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0);"
                                                           class="btn btn-danger btn-remove-item"><i
                                                                    class="fas fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            </template>
                                            <div>
                                                <table class="table table-sm table-striped">
                                                    <thead class="thead-dark">
                                                    <tr>
                                                        <th>@lang('Name')</th>
                                                        <th>@lang('Data type')</th>
                                                        <th>@lang('Is required')</th>
                                                        <th>@lang('Values')</th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="info_container">

                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="5">
                                                            <a id="info_add" href="javascript:void(0);"
                                                               class="btn btn-primary"><i class="fas fa-plus"></i></a>
                                                        </td>
                                                    </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                            @error('info_items')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group required">
                                            <label class="form-label">@lang('Ticket currency')</label>
                                            <select name="ticket_currency" class="form-control">
                                                @foreach(config('events.currencies') as $key => $value)
                                                    <option value="{{ $key }}" {{ $key == $event->ticket_currency ? 'selected' : '' }}>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            @error('ticket_currency')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Tickets')</label>
                                            <small>(@lang('If the event is free, no additional tickets are required'))
                                            </small>
                                            <template id="ticket_template">
                                                <tr>
                                                    <td>
                                                        <input type="text" name="ticket_items[name][]"
                                                               class="form-control" required/>
                                                    </td>
                                                    <td>
                                                        <input type="number" min="0" step="1"
                                                               name="ticket_items[price][]" class="form-control"
                                                               required/>
                                                    </td>

                                                    <td>
                                                        <textarea type="text" name="ticket_items[description][]"
                                                                  class="form-control" row="2" required></textarea>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0);"
                                                           class="btn btn-danger btn-remove-item"><i
                                                                    class="fas fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            </template>
                                            <div>
                                                <table class="table table-sm table-striped">
                                                    <thead class="thead-dark">
                                                    <tr>
                                                        <th>@lang('Name')</th>
                                                        <th>@lang('Price')</th>
                                                        <th>@lang('Description')</th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="ticket_container">

                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="4">
                                                            <a id="ticket_add" href="javascript:void(0);"
                                                               class="btn btn-primary"><i class="fas fa-plus"></i></a>
                                                        </td>
                                                    </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                            @error('ticket_items')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="email_and_notify" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group required">
                                            <label class="form-label">@lang('Notify when register successfully')</label>
                                            <input name="noti_register_success"
                                                   value="{{ old('noti_register_success', $event->noti_register_success) }}"
                                                   class="form-control"/>
                                            <small>@lang('This message is used to show guests when guests register the form successfully.')</small>
                                            <br>
                                            @error('noti_register_success')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <hr>
                                    </div>
                                    <div class="col-md-6">

                                        <div class="form-group required">
                                            <label class="form-label">@lang('Sender name')</label>
                                            <input type="text" name="email_sender_name"
                                                   value="{{ old('email_sender_name', $event->email_sender_name) }}"
                                                   class="form-control"/>
                                            @error('email_sender_name')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group required">
                                            <label class="form-label">@lang('Sender email')</label>
                                            <input type="email" name="email_sender_email"
                                                   value="{{ old('email_sender_email', $event->email_sender_email) }}"
                                                   class="form-control"/>
                                            @error('email_sender_email')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group required">
                                            <label class="form-label">@lang('Email Subject')</label>
                                            <input type="text" name="email_subject"
                                                   value="{{ old('email_subject', $event->email_subject) }}"
                                                   class="form-control"/>
                                            @error('email_subject')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Email content')</label>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <textarea name="email_content" id="email_content" rows="4"
                                                              class="form-control">{{ old('email_content', $event->email_content) }}</textarea>

                                                    @error('email_content')
                                                    <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <small>
                                                        <p>@lang('Enter the following fields so that the content entered by the guest into the form field will be pasted automatically:')</p>
                                                        <ul>
                                                            <li>@lang('Event name'): <strong>%event_name%</strong></li>
                                                            <li>@lang('Event description'):
                                                                <strong>%event_description%</strong></li>
                                                            <li>@lang('Event address'): <strong>%event_address%</strong>
                                                            </li>
                                                            <li>@lang('Event start date'):
                                                                <strong>%event_start_date%</strong></li>
                                                            <li>@lang('QR code'): <strong>%qr_code%</strong></li>

                                                            <li>@lang('Guest fullname'):
                                                                <strong>%guest_fullname%</strong></li>
                                                            <li>@lang('Guest email'): <strong>%guest_email%</strong>
                                                            </li>
                                                            <li>@lang('Guest ticket name'):
                                                                <strong>%guest_ticket_name%</strong></li>
                                                            <li>@lang('Guest ticket price'): <strong>%guest_ticket_price%</strong>
                                                            </li>
                                                            <li>@lang('Guest ticket currency'): <strong>%guest_ticket_currency%</strong>
                                                            </li>
                                                        </ul>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(Module::find('Saas'))
                                <div class="tab-pane fade" id="nav-domains" role="tabpanel"
                                     aria-labelledby="nav-domains-tab">
                                    <span class="">@lang('Public event link:')</span> <a
                                            href="{{ $event->getPublicUrl() }}"
                                            target="_blank">{{ $event->getPublicUrl() }}</a>
                                    <hr>
                                    <h4 class="">@lang('Custom domain')</h4>
                                    @if($allowCustomFonts == true)

                                        <div class="row mt-4">
                                            <div class="col-md-8">
                                                <label class="form-label"><strong>@lang('Step 1:') </strong>@lang('Enter your domain')
                                                </label>
                                                <div class="input-group mt-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">http://</span>
                                                    </div>
                                                    <input type="text" id="custom_domain" name="custom_domain"
                                                           value="{{ $event->custom_domain }}"
                                                           placeholder="@lang('Your domain (domain or subdomain)')"
                                                           class="form-control">
                                                </div>
                                                <small class="text-danger" id="domain_text_error"></small>
                                            </div>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="col-md-12">
                                                <div id="custom_domain_note" class="">
                                                    <p class="text-dark">
                                                        <strong>@lang('Step 2:') </strong>@lang("Add a record below in your domain provider DNS settings")
                                                    </p>
                                                    <table class="table card-table table-vcenter text-nowrap">
                                                        <thead class="thead-dark">
                                                        <tr>
                                                            <th>@lang('TYPE')</th>
                                                            <th>@lang('VALUE')</th>
                                                            <th>@lang('TTL')</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td>A</td>
                                                            <td>{{ config('app.SERVER_IP') }}</td>
                                                            <td>Automatic</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-warning mb-0 p-2">
                                            <small>@lang('You need upgrade for custom domain!')</small>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            <div class="tab-pane fade" id="theme_design" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-5 mb-4">
                                        <div class="form-group required">
                                            <label class="form-label">@lang('Theme')</label>
                                            <select name="theme" class="form-control" id="theme_design_list">
                                                @foreach ($event_templates as $item)
                                                    <option value="{{ $item }}" {{ $event->theme == $item ? 'selected' : ''}}>
                                                        {{ $item }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        @foreach ($event_templates as $item)
                                            <div id="template_{{ $item }}"
                                                 class="mt-2 theme-screen-preview {{ $event->theme != $item ? 'd-none' : ''}}">
                                                @if($allowPremiumTheme == false && getConfigFileEventTemplate($item)['is_premium'] == true)
                                                    <div class="alert alert-warning mb-0 p-2">
                                                        <small>@lang('You need upgrade for premium theme') -
                                                            <strong>{{ $item }}</strong></small>
                                                    </div>
                                                @endif
                                                <a href="{{ getImagePreviewEventTemplate($item) }}" target="_blank">
                                                    <img class="" src="{{ getImagePreviewEventTemplate($item) }}"
                                                         alt="{{ $item }}"/>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Theme color')</label>
                                            <div class="input-group">
                                                <input type="color" name="theme_color" class="form-control"
                                                       value="{{ old('theme_color', $event->theme_color) }}"/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">@lang('Background for theme')</label>
                                            <div class="input-group w-200">
                                                <input type="file" name="background" class="form-control"
                                                       value="{{ old('background', '') }}"
                                                       accept="image/png, image/gif, image/jpeg"/>
                                            </div>
                                            @error('background')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                            @if($event->background)
                                                <p><img src="{{ URL::to('/') }}/storage/{{ $event->background }}"
                                                        class="img-thumbnail"/></p>
                                            @endif
                                        </div>

                                        {{-- Font --}}
                                        <div class="form-group">
                                            <label class="form-label">@lang('Font Currently'):
                                                <strong class="text-info"
                                                        id="font_currently_label">{{ $event->font_family }}</strong>
                                            </label>
                                            @if($allowCustomFonts == true)
                                                <input type="text" id="font_currently" value="{{ $event->font_family }}"
                                                       name="font_family" class="form-control" hidden>
                                                <input type="text" id="search_fonts" name=""
                                                       placeholder="@lang('Find another Google Fonts')"
                                                       class="form-control">
                                                <div class="d-none mt-2" id="spinner-loading-fonts">
                                                    <div class="d-flex align-items-center">
                                                        <strong>@lang('Loading')...</strong>
                                                        <div class="spinner-border ml-auto" role="status"
                                                             aria-hidden="true"></div>
                                                    </div>
                                                </div>
                                                <div id="list_fonts" class="mt-2">
                                                </div>
                                            @else
                                                <div class="alert alert-warning mb-0 p-2">
                                                    <small>@lang('You need upgrade for custom font!')</small>
                                                </div>
                                            @endif
                                        </div>
                                        {{-- Custom header and footer --}}

                                        <div class="form-group">
                                            <label class="form-label">@lang('Custom code header')</label>
                                            @if($allowCustomHeaderFooter == true)
                                                <textarea name="custom_header" rows="4" class="form-control"
                                                          placeholder="@lang('You can add custom css with <style>your css..</style> or integrate 3rd parties like live chat, hotline, notify...')">{{ old('custom_header', $event->custom_header) }}</textarea>
                                            @else
                                                <div class="alert alert-warning mb-0 p-2">
                                                    <small>@lang('You need upgrade for custom code header!')</small>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">@lang('Custom code footer')</label>
                                            @if($allowCustomHeaderFooter == true)
                                                <textarea name="custom_footer" rows="4" class="form-control"
                                                          placeholder="@lang('You can add custom js with <script>your js..</script> or integrate 3rd parties like live chat, hotline, notify...')">{{ old('custom_footer', $event->custom_footer) }}</textarea>
                                            @else
                                                <div class="alert alert-warning mb-0 p-2">
                                                    <small>@lang('You need upgrade for custom code footer!')</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>
                            {{-- SEO and Social --}}
                            <div class="tab-pane fade" id="seo_config" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Favicon')</label>
                                            <input name="favicon" type="file" accept="image/*"><br>
                                            <small>@lang("Image will be displayed in browser tabs (best size 32 x 32)")</small>
                                            @if($event->favicon)
                                                <p><img src="{{ URL::to('/') }}/storage/{{ $event->favicon }}"
                                                        data-value="" class="img-thumbnail"/></p>
                                            @endif
                                        </div>
                                        <hr>
                                        <h4 class="title-tab-content">@lang('SEO Settings')</h4>
                                        <p class="title-break">@lang('Specify here necessary information about your page. It will help search engines find your content')
                                            .</p>
                                        <div class="form-group mb-4 mt-4">
                                            <label class="custom-switch pl-0">
                                                <input type="checkbox" name="seo_enable" value="1"
                                                       class="custom-switch-input" {{ $event->seo_enable ? 'checked' : '' }}>
                                                <span class="custom-switch-indicator"></span>
                                                <span class="custom-switch-description">@lang('Search Engine visibility')</span>
                                            </label>
                                            <br>
                                            <small>@lang('If disabled, the event will not be indexed by search engines, such as Google or Bing.')</small>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">@lang('SEO Title')</label>
                                            <input type="text" name="seo_title" value="{{$event->seo_title}}"
                                                   class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">@lang('SEO Description')</label>
                                            <textarea name="seo_description" rows="3"
                                                      class="form-control">{{$event->seo_description}}</textarea>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">@lang('SEO Keywords')</label>
                                            <textarea name="seo_keywords" rows="3"
                                                      class="form-control">{{$event->seo_keywords}}</textarea>
                                        </div>
                                        <hr>
                                        <h4 class="title-tab-content">@lang('Social Settings')</h4>
                                        <p class="title-break">@lang('Customize how your page is viewed when it is shared on social networks')
                                            .</p>
                                        <div class="form-group">
                                            <label class="form-label">@lang('Social Title')</label>
                                            <input type="text" name="social_title" value="{{$event->social_title}}"
                                                   class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">@lang('Social Image')</label>
                                            <input name="social_image" type="file" accept="image/*"><br>
                                            <small>@lang("Upload an image that will be automatically displayed on your posts, on social media platforms like Facebook and Twitter... To display the photo seamlessly on all platforms, the ideal dimension is 1200x630, with a file size smaller than 300KB")</small>
                                            @if($event->social_image)
                                                <p><img src="{{ URL::to('/') }}/storage/{{ $event->social_image }}"
                                                        data-value="" class="img-thumbnail"/></p>
                                            @endif

                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">@lang('Social Description')</label>
                                            <textarea name="social_description" rows="3"
                                                      class="form-control">{{$event->social_description}}</textarea>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex">
                            <a href="{{ route('events.index') }}" class="btn btn-secondary">@lang('Cancel')</a>
                            <button type="submit" class="btn btn-success ml-auto">@lang('Save')</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@stop

@push('scripts')
    <script>
        var url_search_fonts = `{{ url('getFonts') }}`;
        var _token = '{{ csrf_token() }}';
        var lang = {
            "selected_font": "@lang('Selected font')",
            "select_a_font": "@lang('Select a font')",
            "demo_font": "@lang('Demo')",
            "action": "@lang('Action')",
            "font_name": "@lang('Font name')",
        };
        var event_infos_item = {!! json_encode(old('info_items', $event->info_items)) !!};
        var event_ticket_item = {!! json_encode(old('ticket_items', $event->ticket_items)) !!};
    </script>
    <script src="{{ Module::asset('events:js/events/edit.js') }}"></script>
@endpush

@push('head')
    <link rel="stylesheet" href="{{ Module::asset('events:css/styles.css') }}"/>
@endpush