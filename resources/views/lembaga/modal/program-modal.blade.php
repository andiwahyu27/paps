  @foreach ($docs as $doc)
      <div class="modal fade" id="{{ 'editDokumenModal' . $doc->id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <form action="{{ route('edit.dokumen') }}" method="post" enctype="multipart/form-data">
                      @method('PUT')
                      @csrf
                      <div class="modal-header">
                          <h5 class="modal-title">Edit Dokumen</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                          <div class="row">
                              <div class="col-12 mb-3">
                                  <label for="nama" class="form-label">Nama Dokumen</label>
                                  <input type="text" name="nama" id="nama" class="form-control"
                                      value="{{ $doc->nama }}" />
                              </div>
                              <div class="col-12 mb-1">
                                  <label for="file-dokumen" class="form-label">File Dokumen</label>
                                  <input type="file" name="file-dokumen" id="file-dokumen" class="form-control" />
                              </div>
                              <a href="{{ asset($doc->path_dokumen) }}" target="_blank"><i
                                      class="bx bxs-file-pdf"></i>Lihat File</a>
                          </div>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                              Batal
                          </button>
                          <button type="submit" class="btn btn-warning">Ubah</button>
                          <input type="hidden" name="id" id="id" value="{{ $doc->id }}">
                      </div>
                  </form>
              </div>
          </div>
      </div>

      <div class="modal fade" id="{{ 'hapusDokumenModal' . $doc->id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <form action="{{ route('hapus.dokumen') }}" method="post" enctype="multipart/form-data">
                      @method('delete')
                      @csrf
                      <div class="modal-header">
                          <h5 class="modal-title">Hapus Dokumen</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                          <p>Apakah Anda yakin akan menghapus data ini?</p>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                              Batal
                          </button>
                          <button type="submit" class="btn btn-danger">Hapus</button>
                          <input type="hidden" name="id" id="id" value="{{ $doc->id }}">
                      </div>
                  </form>
              </div>
          </div>
      </div>
  @endforeach

  @push('icon')
      <style>
          a.btn-outline-primary:active,
          a.btn-outline-primary:hover,
          #doc.btn-outline-primary:hover {
              color: #fff;
              background-color: #5f61e6;
              border-color: #5f61e6;
              box-shadow: 0 0.125rem 0.25rem 0 rgba(105, 108, 255, 0.4);
              transform: translateY(-1px);
          }

          a.btn-outline-primary,
          a.btn-outline-primary:focus,
          #doc:focus {
              color: #696cff;
              border-color: #696cff;
              background: transparent;
          }
      </style>
  @endpush
